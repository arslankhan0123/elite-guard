@if($scans->isNotEmpty())
    <table class="scan-grid">
        @foreach($scans->chunk(3) as $scanRow)
            <tr>
                @foreach($scanRow as $scan)
                    <td style="width: {{ 100 / $scanRow->count() }}%;">
                        <table class="scan-evidence-card">
                            <tr>
                                @if($scan->image)
                                    <td class="scan-photo-cell">
                                        <img class="evidence-image" src="{{ $resolvePdfImage($scan->image) }}" alt="Scan evidence">
                                    </td>
                                @endif
                                <td class="scan-data-cell">
                                    <div class="scan-tag">{{ $scan->nfcTag?->name ?? 'Unknown Tag' }}</div>
                                    <div><strong>UID:</strong> {{ $scan->nfcTag?->uid ?? 'N/A' }}</div>
                                    <div><strong>Time:</strong> {{ $scan->time ? \Carbon\Carbon::parse($scan->time)->format('h:i:s A') : 'N/A' }}</div>
                                    <div><strong>By:</strong> {{ $scan->user?->name ?? $fallbackUser ?? 'N/A' }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>
@else
    <span class="evidence-line empty">No NFC tags scanned.</span>
@endif
