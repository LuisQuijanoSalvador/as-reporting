<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Empresa;
use Livewire\Component;
use Livewire\Attributes\On;  // Importar el atributo On
use Illuminate\Support\Facades\Hash;

class UsuarioForm extends Component
{
    public $usuario_id;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role = 'user';
    public $empresa_id;
    public $empresas;
    public $showModal = false;
    public $modalTitle = 'Nuevo Usuario';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:admin,user',
        'empresa_id' => 'required|exists:vista_empresas,id',
    ];

    #[On('openModal')]  // Usar el atributo On
    public function openModal()
    {
        $this->resetInputFields();
        $this->modalTitle = 'Nuevo Usuario';
        $this->showModal = true;
    }

    #[On('editUsuario')]  // Usar el atributo On
    public function editUsuario($id)
    {
        $usuario = User::findOrFail($id);
        $this->usuario_id = $id;
        $this->name = $usuario->name;
        $this->email = $usuario->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = $usuario->role;
        $this->empresa_id = $usuario->empresa_id;
        $this->modalTitle = 'Editar Usuario';
        $this->showModal = true;
    }

    public function mount()
    {
        $this->empresas = Empresa::all();
    }

    public function render()
    {
        return view('livewire.usuario-form');
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function resetInputFields()
    {
        $this->usuario_id = '';
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'user';
        $this->empresa_id = '';
    }

    public function save()
    {
        if ($this->usuario_id) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function store()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'empresa_id' => $this->empresa_id,
        ]);

        $this->closeModal();
        $this->resetInputFields();
        
        session()->flash('message', 'Usuario creado correctamente.');
        $this->dispatch('usuarioCreado');
    }

    public function update()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$this->usuario_id,
            'role' => 'required|in:admin,user',
            'empresa_id' => 'required|exists:vista_empresas,id',
        ]);

        if ($this->password) {
            $this->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $validatedData['password'] = Hash::make($this->password);
        }

        $usuario = User::find($this->usuario_id);
        $usuario->update($validatedData);

        $this->closeModal();
        $this->resetInputFields();
        
        session()->flash('message', 'Usuario actualizado correctamente.');
        $this->dispatch('usuarioActualizado');
    }
}