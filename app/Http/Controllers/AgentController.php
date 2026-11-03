<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Rules\Cpf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * CRUD de agentes (usuários com role = AGENT). Só o admin acessa (Gate "manage-agents", ver routes/web.php).
 * Usa o model User: a senha sai criptografada (cast "hashed") e o login é o mesmo do resto do sistema.
 */
class AgentController extends Controller
{
    public function index(): View
    {
        return view('agents.index', [
            'agents' => User::agents()->orderBy('name')->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['cpf' => Cpf::digits($request->input('cpf'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'cpf' => ['required', 'string', new Cpf, 'unique:users,cpf'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $agent = new User($data);
        $agent->role = UserRole::AGENT;
        $agent->email_verified_at = now(); // quem cadastra é o admin: o e-mail dele vale como confirmação
        $agent->save();

        return redirect()->route('agents.index')->with('status', __('Agente cadastrado com sucesso.'));
    }

    public function edit(User $agent): View
    {
        $this->ensureAgent($agent);

        return view('agents.edit', ['agent' => $agent]);
    }

    public function update(Request $request, User $agent): RedirectResponse
    {
        $this->ensureAgent($agent);

        $request->merge(['cpf' => Cpf::digits($request->input('cpf'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($agent->getKey(), 'id_user')],
            'cpf' => ['required', 'string', new Cpf,
                Rule::unique('users', 'cpf')->ignore($agent->getKey(), 'id_user')],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
        ]);

        if (empty($data['password'])) {
            unset($data['password']); // em branco = mantém a senha atual
        }

        $agent->fill($data);

        if ($agent->isDirty('email')) {
            $agent->email_verified_at = now();
        }

        $agent->save();

        return redirect()->route('agents.index')->with('status', __('Agente atualizado com sucesso.'));
    }

    public function destroy(User $agent): RedirectResponse
    {
        $this->ensureAgent($agent);

        $agent->delete();

        return redirect()->route('agents.index')->with('status', __('Agente removido.'));
    }

    /**
     * Nunca mexe em cidadão ou admin por esta rota: qualquer id que não seja de agente vira 404.
     */
    private function ensureAgent(User $user): void
    {
        abort_unless($user->isAgent(), 404);
    }
}
