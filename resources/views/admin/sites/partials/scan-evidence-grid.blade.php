@if($scans->isNotEmpty())
    <table class="scan-grid">
        @foreach($scans->chunk(3) as $scanRow)
            <tr>
                @foreach($scanRow as $scan)
                    @php
                        $imageSource = $scan->image ? $resolvePdfImage($scan->image) : null;
                    @endphp
                    <td class="scan-grid-cell">
                        <table class="scan-evidence-card">
                            <tr>
                                <td class="scan-data-cell">
                                    <div class="scan-tag">{{ $scan->nfcTag?->name ?? 'Unknown Tag' }}</div>
                                    <div><strong>UID:</strong> {{ $scan->nfcTag?->uid ?? 'N/A' }}</div>
                                    <div><strong>Time:</strong> {{ $scan->time ? \Carbon\Carbon::parse($scan->time)->format('h:i:s A') : 'N/A' }}</div>
                                    <div><strong>By:</strong> {{ $scan->user?->name ?? $fallbackUser ?? 'N/A' }}</div>
                                </td>
                                @if($imageSource)
                                    <td class="scan-photo-cell">
                                        <img class="evidence-image" src="{{ $imageSource }}" alt="Evidence">
                                    </td>
                                @else
                                    <td class="scan-photo-cell">
                                        <div class="no-photo">No Image</div>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    </td>
                @endforeach
                @for($emptyCell = $scanRow->count(); $emptyCell < 3; $emptyCell++)
                    <td class="scan-grid-cell scan-grid-empty"></td>
                @endfor
            </tr>
        @endforeach
    </table>
@else
    <span class="evidence-line empty">No NFC tags scanned.</span>
@endif
