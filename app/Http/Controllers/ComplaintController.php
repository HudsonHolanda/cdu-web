<?php

namespace App\Http\Controllers;

use App\Enums\ComplaintStatus;
use App\Enums\UserRole;
use App\Models\Complaint;
use App\Models\ComplaintImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ComplaintController extends Controller
{
    private const CATEGORIES = [
        'Buraco na via', 'Iluminação pública', 'Lixo acumulado',
        'Vazamento de água', 'Pragas e animais', 'Outros',
    ];

    public function index(Request $request)
    {
        $search = trim((string) $request->query('busca', ''));
        $query = Complaint::query()->with('images')->latest();

        if ($search !== '') {
            $term = '%'.mb_strtolower($search).'%';
            $query->where(fn ($q) => $q->whereRaw('LOWER(title) LIKE ?', [$term])
                ->orWhereRaw('LOWER(description) LIKE ?', [$term])
                ->orWhereRaw('LOWER(address) LIKE ?', [$term])
                ->orWhereRaw('LOWER(district) LIKE ?', [$term]));
        }

        $items = [];
        foreach ($query->get() as $complaint) {
            $items[] = $this->toViewData($complaint);
        }
        $complaints = collect($items);

        return view('complaints.index', compact('complaints', 'search'));
    }

    public function mine(Request $request)
    {
        $search = trim((string) $request->query('busca', ''));
        $query = Complaint::query()
            ->with('images')
            ->where('id_user', $request->user()->id_user)
            ->latest();

        if ($search !== '') {
            $term = '%'.mb_strtolower($search).'%';
            $query->where(fn ($q) => $q->whereRaw('LOWER(title) LIKE ?', [$term])
                ->orWhereRaw('LOWER(description) LIKE ?', [$term])
                ->orWhereRaw('LOWER(address) LIKE ?', [$term])
                ->orWhereRaw('LOWER(district) LIKE ?', [$term]));
        }

        $items = [];
        foreach ($query->get() as $complaint) {
            $items[] = $this->toViewData($complaint);
        }

        return view('complaints.index', [
            'complaints' => collect($items),
            'search' => $search,
            'myComplaints' => true,
        ]);
    }

    public function show(string $publicId)
    {
        $complaint = Complaint::query()->with('images')->where('public_id', $publicId)->firstOrFail();

        return view('complaints.show', ['complaint' => $this->toViewData($complaint)]);
    }

    public function create()
    {
        return view('complaints.create', ['categories' => self::CATEGORIES]);
    }

    public function edit(Complaint $complaint)
    {
        $this->authorize('update', $complaint);

        return view('complaints.edit', [
            'complaint' => $this->toViewData($complaint->load('images')),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Complaint::class);

        $validated = $request->validate([
            'category' => ['required', 'string', Rule::in(self::CATEGORIES)],
            'title' => ['required', 'string', 'min:8', 'max:120'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'address' => ['required', 'string', 'max:180'],
            'district' => ['nullable', 'string', 'max:100'],
            'cep' => ['nullable', 'string', 'max:9', 'regex:/^\d{5}-?\d{3}$/'],
            'complement' => ['nullable', 'string', 'max:160'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'photos' => ['required', 'array', 'min:1', 'max:5'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ], [
            'photos.required' => 'Adicione ao menos uma foto para registrar o relato.',
            'photos.min' => 'Adicione ao menos uma foto para registrar o relato.',
            'photos.max' => 'Você pode enviar no máximo cinco fotos.',
            'photos.*.image' => 'Os anexos devem ser arquivos de imagem.',
            'photos.*.mimes' => 'Use imagens JPG, PNG ou WebP.',
            'photos.*.max' => 'Cada foto deve ter no máximo 5 MB.',
            'cep.regex' => 'Informe um CEP válido, como 00000-000.',
        ]);

        $disk = config('filesystems.default');
        $paths = [];

        try {
            $complaint = DB::transaction(function () use ($request, $validated, $disk, &$paths) {
                $complaint = new Complaint;
                $complaint->public_id = (string) Str::uuid();
                $complaint->id_user = $request->user()->id_user;
                $complaint->category = $validated['category'];
                $complaint->title = $validated['title'];
                $complaint->description = $validated['description'];
                $complaint->status = ComplaintStatus::PENDING;
                $complaint->high_priority = false;
                $complaint->latitude = $validated['latitude'];
                $complaint->longitude = $validated['longitude'];
                $complaint->address = $validated['address'];
                $complaint->district = $validated['district'] ?? '';
                $complaint->cep = isset($validated['cep']) ? str_replace('-', '', $validated['cep']) : '';
                $complaint->complement = $validated['complement'] ?? null;
                $complaint->save();

                foreach ($request->file('photos', []) as $photo) {
                    $path = $photo->store('complaints/'.$complaint->complaint_id, $disk);
                    if (! $path) {
                        throw new \RuntimeException('Falha ao armazenar uma imagem.');
                    }
                    $paths[] = $path;
                    $complaint->images()->create(['path' => $path, 'disk' => $disk]);
                }

                $complaint->image_url = $paths[0] ?? null;
                $complaint->save();

                return $complaint->load('images');
            });
        } catch (\Throwable $exception) {
            foreach ($paths as $path) {
                Storage::disk($disk)->delete($path);
            }
            report($exception);

            return back()->withInput()->withErrors(['photos' => 'Não foi possível salvar o relato agora. Tente novamente.']);
        }

        return redirect()->route('complaints.show', ['publicId' => $complaint->public_id])
            ->with('success', 'Sua denúncia foi registrada.');
    }

    public function update(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);

        $validated = $request->validate([
            'category' => ['sometimes', 'required', 'string', Rule::in(self::CATEGORIES)],
            'title' => ['sometimes', 'required', 'string', 'min:8', 'max:120'],
            'description' => ['sometimes', 'required', 'string', 'min:20', 'max:2000'],
            'address' => ['sometimes', 'required', 'string', 'max:180'],
            'district' => ['sometimes', 'nullable', 'string', 'max:100'],
            'cep' => ['sometimes', 'nullable', 'string', 'max:9', 'regex:/^\d{5}-?\d{3}$/'],
            'complement' => ['sometimes', 'nullable', 'string', 'max:160'],
            'latitude' => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'required', 'numeric', 'between:-180,180'],
            'photos' => ['sometimes', 'array', 'max:5'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);
        $existingImageCount = $complaint->images()->count();
        if ($existingImageCount === 0 && $complaint->image_url) {
            $existingImageCount = 1;
        }
        if ($existingImageCount + count($request->file('photos', [])) > 5) {
            return back()->withInput()->withErrors(['photos' => 'O relato pode ter no máximo cinco imagens.']);
        }

        foreach (['category', 'title', 'description', 'address', 'district', 'complement', 'latitude', 'longitude'] as $field) {
            if (array_key_exists($field, $validated)) {
                $complaint->{$field} = $validated[$field];
            }
        }
        if (array_key_exists('cep', $validated)) {
            $complaint->cep = str_replace('-', '', (string) $validated['cep']);
        }
        $disk = config('filesystems.default');
        $paths = [];
        try {
            DB::transaction(function () use ($request, $complaint, $disk, &$paths) {
                foreach ($request->file('photos', []) as $photo) {
                    $path = $photo->store('complaints/'.$complaint->complaint_id, $disk);
                    if (! $path) {
                        throw new \RuntimeException('Falha ao armazenar uma imagem.');
                    }
                    $paths[] = $path;
                    $complaint->images()->create(['path' => $path, 'disk' => $disk]);
                }
                if ($paths && $complaint->images()->count() > 5) {
                    throw new \InvalidArgumentException('O relato pode ter no máximo cinco imagens.');
                }
                if ($paths && ! $complaint->image_url) {
                    $complaint->image_url = $paths[0];
                }
                $complaint->save();
            });
        } catch (\Throwable $exception) {
            foreach ($paths as $path) {
                Storage::disk($disk)->delete($path);
            }
            throw $exception;
        }

        return redirect()->route('complaints.show', ['publicId' => $complaint->public_id])->with('success', 'Relato atualizado.');
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        abort_unless($request->user()->hasRole(UserRole::AGENT), 403);
        $validated = $request->validate([
            'status' => ['required', 'integer', Rule::in(array_map(fn (ComplaintStatus $status) => $status->value, ComplaintStatus::cases()))],
            'high_priority' => ['sometimes', 'boolean'],
        ]);

        DB::transaction(function () use ($request, $complaint, $validated) {
            $from = $complaint->status?->value;
            $to = ComplaintStatus::from((int) $validated['status']);
            $complaint->status = $to;
            if (array_key_exists('high_priority', $validated)) {
                $complaint->high_priority = $validated['high_priority'];
            }
            $complaint->save();

            if ($from !== $to->value) {
                DB::table('complaint_status_histories')->insert([
                    'complaint_id' => $complaint->complaint_id,
                    'from_status' => $from,
                    'to_status' => $to->value,
                    'changed_by' => $request->user()->id_user,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('complaints.show', ['publicId' => $complaint->public_id])->with('success', 'Status atualizado.');
    }

    public function destroy(Complaint $complaint)
    {
        $this->authorize('delete', $complaint);
        $images = $complaint->images()->get();
        $complaint->delete();
        foreach ($images as $image) {
            Storage::disk($image->disk)->delete($image->path);
        }

        return redirect()->route('complaints.mine')->with('success', 'Relato excluído.');
    }

    public function image(string $filename): Response
    {
        abort_if($filename !== basename($filename) || $filename === '', 404);
        $image = ComplaintImage::query()->where('path', 'like', '%/'.$filename)->first();

        if ($image) {
            abort_unless(Storage::disk($image->disk)->exists($image->path), 404);
            return Storage::disk($image->disk)->response($image->path);
        }

        // Compatibilidade para imagens já referenciadas pelos dados JSON de demonstração.
        $legacyPath = resource_path('denuncias/'.$filename);
        abort_unless(is_file($legacyPath), 404);

        return response()->file($legacyPath);
    }

    public function geocode(Request $request)
    {
        $data = $request->validate([
            'endereco' => ['nullable', 'string', 'max:180'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'max:100'],
            'cep' => ['nullable', 'string', 'max:9'],
        ]);

        $client = Http::acceptJson()->withHeaders([
            'User-Agent' => 'C.D.U/1.0 (+'.config('app.url', 'http://127.0.0.1:8000').')',
            'Referer' => config('app.url', 'http://127.0.0.1:8000').'/',
        ])->retry(2, 300)->timeout(10);

        $street = implode(', ', array_filter([$data['endereco'] ?? '', $data['bairro'] ?? '']));
        $response = $client->get('https://nominatim.openstreetmap.org/search', [
            'street' => $street,
            'city' => $data['cidade'] ?? '',
            'state' => $data['estado'] ?? '',
            'postalcode' => preg_replace('/\D/', '', $data['cep'] ?? ''),
            'country' => 'Brazil',
            'format' => 'jsonv2',
            'limit' => 1,
        ]);

        $result = $response->json()[0] ?? null;

        if (! $result) {
            $freeText = implode(', ', array_filter([
                $data['endereco'] ?? '',
                $data['bairro'] ?? '',
                $data['cidade'] ?? '',
                $data['estado'] ?? '',
                'Brasil',
            ]));

            $fallback = $client->get('https://nominatim.openstreetmap.org/search', [
                'q' => $freeText,
                'format' => 'jsonv2',
                'limit' => 1,
                'countrycodes' => 'br',
            ]);
            $result = $fallback->json()[0] ?? null;
        }

        if (! $response->successful() || ! $result || ! isset($result['lat'], $result['lon'])) {
            return response()->json(['coordinates' => null]);
        }

        return response()->json(['coordinates' => [
            'latitude' => (float) $result['lat'],
            'longitude' => (float) $result['lon'],
        ]]);
    }

    private function toViewData(Complaint $complaint): array
    {
        $paths = $complaint->images->pluck('path')->all();
        if ($paths === [] && $complaint->image_url) {
            $paths = [$complaint->image_url];
        }
        $filenames = array_map(fn (string $path) => basename($path), $paths);
        $status = $complaint->status?->label() ?? 'Em aberto';

        return [
            'complaintId' => $complaint->complaint_id,
            'publicId' => $complaint->public_id,
            'category' => $complaint->category ?? 'Relato cidadão',
            'title' => $complaint->title,
            'description' => $complaint->description,
            'userId' => $complaint->id_user,
            'imageUrl' => $filenames[0] ?? '',
            'attachments' => $filenames,
            'status' => $status,
            'highPriority' => (bool) $complaint->high_priority,
            'createdAt' => $complaint->created_at?->toIso8601String(),
            'location' => [
                'latitude' => (float) $complaint->latitude,
                'longitude' => (float) $complaint->longitude,
                'address' => $complaint->address,
                'district' => $complaint->district,
                'cep' => $complaint->cep,
                'complement' => $complaint->complement ?? '',
            ],
        ];
    }
}
