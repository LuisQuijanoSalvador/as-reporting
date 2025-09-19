<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class LogosClientes extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Propiedades de estado
    public $showModal = false;
    public $clienteId; // ID de la tabla 'clientes' local
    public $idclienteBackoffice; // ID de la vista 'vista_empresas'
    public $logo; // Archivo de logo subido
    public $logoPreview; // URL temporal para la vista previa
    public $search = '';

    // Propiedades para la lista de clientes en el modal
    public $empresas;

    protected $rules = [
        'idclienteBackoffice' => 'required|integer',
        'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2MB
    ];

    // Validar el campo de logo solo si se está subiendo
    public function getRules()
    {
        if ($this->clienteId) {
            // En modo edición, el logo es opcional
            return array_merge($this->rules, [
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }
        return $this->rules;
    }

    public function mount()
    {
        // Cargar la lista de empresas para el select del modal
        $this->empresas = Empresa::all();
    }

    // Renderizar la vista
    public function render()
    {
        // Unimos los datos de la vista_empresas con los logos guardados localmente
        $logos = DB::table('vista_empresas')
                    ->leftJoin('clientes', 'vista_empresas.id', '=', 'clientes.idcliente')
                    ->select('vista_empresas.id as id', 'vista_empresas.razonSocial', 'clientes.logo')
                    ->when($this->search, function ($query) {
                        $query->where('vista_empresas.razonSocial', 'like', '%' . $this->search . '%');
                    })
                    ->paginate(4);

        return view('livewire.logos-clientes', [
            'logos' => $logos,
        ]);
    }

    // Abrir modal para crear nuevo logo
    public function newLogo()
    {
        $this->reset(['clienteId', 'idclienteBackoffice', 'logo', 'logoPreview']);
        // Emite un evento JS para abrir el modal
        $this->dispatch('open-modal');
    }

    public function editLogo($id)
    {
        $this->resetValidation();
        $this->idclienteBackoffice = $id;

        $logoCliente = Cliente::where('idcliente', $id)->first();
        
        if ($logoCliente) {
            $this->clienteId = $logoCliente->id;
            $this->logoPreview = $logoCliente->logo;
        } else {
            $this->clienteId = null;
            $this->logoPreview = null;
        }
        
        // Emite un evento JS para abrir el modal
        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate($this->getRules());

        DB::beginTransaction();
        try {
            $logoPath = null;

            // Solo guardar el archivo si se subió uno nuevo
            if ($this->logo) {
                $logoPath = $this->logo->store('logos', 'public');
            }

            if ($this->clienteId) {
                // Actualizar cliente existente
                $cliente = Cliente::find($this->clienteId);
                if ($logoPath) {
                    $cliente->logo = $logoPath;
                }
                $cliente->save();
            } else {
                // Crear nuevo cliente con logo
                Cliente::create([
                    'idcliente' => $this->idclienteBackoffice,
                    'logo' => $logoPath,
                ]);
            }

            DB::commit();
            $this->dispatch('close-modal');
            session()->flash('success', 'Logo guardado con éxito!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('close-modal');
            session()->flash('error', 'Ocurrió un error al guardar el logo.');
        }
    }

    // Cerrar modal y limpiar
    public function closeModal()
    {
        $this->reset(['showModal', 'clienteId', 'idclienteBackoffice', 'logo', 'logoPreview']);
    }
}