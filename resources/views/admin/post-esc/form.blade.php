<div class="mb-3">
    <label for="date" class="form-label fw-bold">Date</label>
    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', isset($post) ? $post->date->format('Y-m-d') : date('Y-m-d')) }}" required>
    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="subject" class="form-label fw-bold">Subject</label>
    <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject', $post->subject ?? '') }}" placeholder="Enter subject" required>
    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="long_description" class="form-label fw-bold">Long Description</label>
    <textarea name="long_description" id="long_description" rows="6" class="form-control @error('long_description') is-invalid @enderror" placeholder="Enter full description" required>{{ old('long_description', $post->long_description ?? '') }}</textarea>
    @error('long_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="pdf" class="form-label fw-bold">PDF Attachment <span class="text-muted fw-normal">(optional, max 10 MB)</span></label>
    <input type="file" name="pdf" id="pdf" class="form-control @error('pdf') is-invalid @enderror" accept="application/pdf,.pdf">
    @error('pdf')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if(isset($post) && $post->pdf_path)
        <div class="mt-2">
            <a href="{{ route('post-esc.download', $post->id) }}" target="_blank" rel="noopener">Download current PDF</a>
            <small class="text-muted ms-2">Upload a new PDF to replace it.</small>
        </div>
    @endif
</div>
