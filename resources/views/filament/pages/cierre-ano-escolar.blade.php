<x-filament-panels::page>
<div class="space-y-6">
        <x-filament::section heading="Filtros de Cierre Académico" description="Seleccione la sección y la asignatura para calcular automáticamente las notas definitivas y el estatus de aprobación.">
            {{ $this->form }}
        </x-filament::section>

        @if($this->seccion_id && $this->asignatura_id)
            <x-filament::section heading="Cuadro Resumen de Rendimiento Anual" description="Promedio ponderado de los tres lapsos y condición final del estudiante (Nota aprobatoria mínima: 10 pts).">
                {{ $this->table }}
            </x-filament::section>
        @else
            <div class="p-6 text-center bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">Seleccione la Sección y la Asignatura en la parte superior para visualizar el cuadro de cierre anual.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
