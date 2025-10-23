<?php

// app/Livewire/ClientFieldConfigurator.php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Empresa;
use App\Models\ClientFieldConfiguration;

class ClientFieldConfigurator extends Component
{
    public $clients;
    public $selectedClientId;
    public $config;
    public $cod1_is_visible = false, $cod1_label = 'Cod1', $cod2_is_visible = false, $cod2_label = 'Cod2', $cod3_is_visible = false, $cod3_label = 'Cod3', $cod4_is_visible = false, $cod4_label = 'Cod4';

    protected $rules = [
        'config.cod1_is_visible' => 'nullable|boolean',
        'config.cod1_label' => 'nullable|string|max:50',
        'config.cod2_is_visible' => 'nullable|boolean',
        'config.cod2_label' => 'nullable|string|max:50',
        'config.cod3_is_visible' => 'nullable|boolean',
        'config.cod3_label' => 'nullable|string|max:50',
        'config.cod4_is_visible' => 'nullable|boolean',
        'config.cod4_label' => 'nullable|string|max:50',
    ];

    public function mount()
    {
        // Carga tus clientes, asumo que tienes un modelo Client
        $this->clients = Empresa::select('id', 'razonSocial')->get();
        
        // Inicializa la configuración
        $this->config = new ClientFieldConfiguration();
    }

    public function updatedSelectedClientId($clientId)
    {
        if ($clientId) {
            // Carga la configuración existente o crea una nueva
            $this->config = ClientFieldConfiguration::firstOrNew([
                'client_id' => $clientId
            ]);

            if (!$this->config->exists) {
                $this->config->cod1_is_visible = false;
                $this->config->cod1_label = '';
                // ... otras inicializaciones
            }
        } else {
            // Reinicia si no hay cliente seleccionado
            $this->config = new ClientFieldConfiguration();
        }
    }

    public function saveConfiguration()
    {
        $this->validate();

        if (!$this->selectedClientId) {
            session()->flash('error', 'Por favor, selecciona un cliente.');
            return;
        }

        // Asegura que la configuración tenga el client_id antes de guardar o actualizar
        $this->config->client_id = $this->selectedClientId;
        $this->config->cod1_is_visible = $this->cod1_is_visible;
        $this->config->cod1_label = $this->cod1_label;
        $this->config->cod2_is_visible = $this->cod2_is_visible;
        $this->config->cod2_label = $this->cod2_label;
        $this->config->cod3_is_visible = $this->cod3_is_visible;
        $this->config->cod3_label = $this->cod3_label;
        $this->config->cod4_is_visible = $this->cod4_is_visible;
        $this->config->cod4_label = $this->cod4_label;

        // dd($this->config->toArray());

        $this->config->save();

        session()->flash('message', 'Configuración guardada exitosamente para el cliente ' . $this->config->client->razonSocial);
        $this->selectedClientId = null;
    }

    public function render()
    {
        return view('livewire.client-field-configurator');
    }
}