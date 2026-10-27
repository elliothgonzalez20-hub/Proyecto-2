<x-filament-panels::page>
    <div class="space-y-6">

            <x-filament::section heading="Filtros de Carga" description="Seleccione la sección, la asignatura y el lapso para desplegar la planilla de notas.">
                {{ $this->form }}
            </x-filament::section>

            @if($this->seccion_id && $this->asignatura_id && $this->lapso)
                <x-filament::section heading="Planilla de Registro Estudiantil" description="Ingrese la nota definitiva de cada estudiante (escala del 1 al 20). Los cambios se guardan automáticamente al escribir.">
                    {{ $this->table }}
                </x-filament::section>
            @else
                <div class="p-6 text-center bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Por favor, seleccione la Sección, la Asignatura y el Lapso en los filtros superiores para comenzar la carga de calificaciones.</p>
                </div>
            @endif
    </div>
</x-filament-panels::page>
