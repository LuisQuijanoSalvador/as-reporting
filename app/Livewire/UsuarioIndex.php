<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;  // Importar el atributo On
use Livewire\WithPagination;

class UsuarioIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    #[On('usuarioCreado')]  // Usar el atributo On
    public function refreshOnCreated() {}

    #[On('usuarioActualizado')]  // Usar el atributo On
    public function refreshOnUpdated() {}

    #[On('usuarioEliminado')]  // Usar el atributo On
    public function refreshOnDeleted() {}

    public function render()
    {
        $usuarios = User::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('email', 'like', '%'.$this->search.'%')
            ->paginate($this->perPage);

        return view('livewire.usuario-index', [
            'usuarios' => $usuarios
        ])->layout('layouts.app');
    }

    public function deleteUsuario($id)
    {
        User::find($id)->delete();
        $this->dispatch('usuarioEliminado');
        session()->flash('message', 'Usuario eliminado correctamente.');
    }
}