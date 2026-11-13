<?php

namespace App\Http\Controllers;

use App\Services\ComplaintRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ComplaintController extends Controller
{
    private const CATEGORIES = [
        'Buraco na via',
        'Iluminação pública',
        'Lixo acumulado',
        'Vazamento de água',
        'Pragas e animais',
        'Outros',
    ];

    public function index(Request $request, ComplaintRepository $complaints)
    {
        $items = $complaints->all();
        $search = trim((string) $request->query('busca', ''));

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $items = $items->filter(fn (array $item) => str_contains(
                mb_strtolower(($item['title'] ?? '').' '.($item['description'] ?? '').' '.($item['location']['address'] ?? '')),
                $needle,
            ));
        }

        return view('complaints.index', [
            'complaints' => $items->values(),
            'search' => $search,
        ]);
    }

    public function show(string $publicId, ComplaintRepository $complaints)
    {
        $complaint = $complaints->findByPublicId($publicId);
        abort_if($complaint === null, 404);

        return view('complaints.show', ['complaint' => $complaint]);
    }

    public function create()
    {
        return view('complaints.create', ['categories' => self::CATEGORIES]);
    }

    public function store(Request $request, ComplaintRepository $complaints)
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'in:'.implode(',', self::CATEGORIES)],
            'title' => ['required', 'string', 'min:8', 'max:120'],
            'description' => ['required', 'string', 'min:20', 'max:2000'],
            'address' => ['required', 'string', 'max:180'],
            'district' => ['required', 'string', 'max:100'],
            'cep' => ['required', 'string', 'max:9', 'regex:/^\d{5}-?\d{3}$/'],
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

        $uploadedFilenames = [];

        try {
            foreach ($request->file('photos', []) as $photo) {
                $filename = Str::uuid().'.'.$photo->extension();
                $photo->move(resource_path('denuncias'), $filename);
                $uploadedFilenames[] = $filename;
            }

            $complaint = [
                'complaintId' => 'DEN-'.now()->format('Y').'-'.Str::upper(Str::random(8)),
                'publicId' => (string) Str::uuid(),
                'category' => $validated['category'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'userId' => $request->user()->getAuthIdentifier(),
                'imageUrl' => $uploadedFilenames[0],
                'attachments' => $uploadedFilenames,
                'status' => 'Em aberto',
                'highPriority' => false,
                'createdAt' => now()->toIso8601String(),
                'location' => [
                    'latitude' => (float) $validated['latitude'],
                    'longitude' => (float) $validated['longitude'],
                    'address' => $validated['address'],
                    'district' => $validated['district'],
                    'cep' => $validated['cep'],
                    'complement' => $validated['complement'] ?? '',
                ],
            ];

            $complaints->create($complaint);
        } catch (\Throwable $exception) {
            foreach ($uploadedFilenames as $filename) {
                File::delete(resource_path('denuncias/'.$filename));
            }

            report($exception);
            return back()->withInput()->withErrors(['photos' => 'Não foi possível salvar o relato agora. Tente novamente.']);
        }

        return redirect()->route('complaints.show', ['publicId' => $complaint['publicId']])
            ->with('success', 'Sua denúncia foi registrada.');
    }

    public function image(string $filename, ComplaintRepository $complaints): BinaryFileResponse|Response
    {
        $allowed = $complaints->all()
            ->flatMap(fn (array $item) => array_merge([$item['imageUrl'] ?? null], $item['attachments'] ?? []))
            ->filter()
            ->map(fn ($path) => basename((string) $path))
            ->unique();

        abort_unless($allowed->contains($filename), 404);

        $path = resource_path('denuncias/'.$filename);
        abort_unless(File::isFile($path), 404);

        return response()->file($path);
    }
}
