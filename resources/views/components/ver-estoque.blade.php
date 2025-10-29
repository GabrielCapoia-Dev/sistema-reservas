@php
    // Helpers simples de formatação BR
    $fmtInt = fn($v) => number_format((int)$v, 0, ',', '.');
    $fmtMoeda = fn($v) => 'R$ ' . number_format((float)$v, 2, ',', '.');
@endphp

<div class="space-y-4">
    <div class="overflow-x-auto rounded-xl border">
        <table class="min-w-full divide-y">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left text-sm font-semibold">Estoque</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Produto</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Categoria</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold">Quantidade</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold">Preço</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($linhas as $linha)
                    <tr>
                        <td class="px-4 py-3 text-sm">{{ $linha['estoque'] }}</td>
                        <td class="px-4 py-3 text-sm">{{ $linha['produto'] }}</td>
                        <td class="px-4 py-3 text-sm">{{ $linha['categoria'] }}</td>
                        <td class="px-4 py-3 text-sm text-right">{{ $fmtInt($linha['quantidade']) }}</td>
                        <td class="px-4 py-3 text-sm text-right">{{ $fmtMoeda($linha['preco']) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-medium">{{ $fmtMoeda($linha['total']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-sm text-gray-500" colspan="6">
                            Nenhum produto vinculado a este estoque.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(($totalGeral ?? 0) > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-4 py-3 text-sm font-semibold" colspan="5">Total geral</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold">{{ $fmtMoeda($totalGeral) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
