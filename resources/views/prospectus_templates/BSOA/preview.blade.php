<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>BSOA Prospectus – Student’s Copy</title>
<style>
@include('prospectus_templates.BSOA._styles')

/* ---- screen-only preview chrome ---- */
body { background: #e9e9e9; }
.toolbar { position: sticky; top: 0; z-index: 10; background: #1f3a7a; color: #fff; padding: 10px 16px; display: flex; gap: 10px; align-items: center; font-family: Arial, sans-serif; font-size: 14px; }
.toolbar .title { flex: 1; font-weight: bold; }
.toolbar a, .toolbar button { background: #fff; color: #1f3a7a; border: 0; border-radius: 4px; padding: 7px 14px; font-size: 14px; font-weight: bold; text-decoration: none; cursor: pointer; }
.toolbar a:hover, .toolbar button:hover { background: #ffd966; }
.scroll { overflow-x: auto; }
.preview-modal { width: 96vw; max-width: 11in; max-height: 92vh; padding: 0; border: 0; border-radius: 6px; overflow: hidden; background: #fff; }
.preview-modal::backdrop { background: rgba(0, 0, 0, .65); }
.preview-modal-header { position: sticky; top: 0; }
.preview-modal-header .title { white-space: nowrap; }
.preview-modal-header .close-button { white-space: nowrap; }
.preview-modal-scroll { max-height: calc(92vh - 48px); overflow: auto; background: #e9e9e9; }
@media screen { .sheet { width: 8.4in; padding: 0.3in; margin: 12px auto; box-shadow: 0 0 6px rgba(0,0,0,.25); } }
@media print { body { background: #fff; } .toolbar, .preview-modal { display: none; } .sheet { width: 7.8in; margin: 0; padding: 0; box-shadow: none; } }
.embedded-preview .scroll { min-height: 100vh; overflow: auto; }
</style></head><body @class(['embedded-preview' => request()->boolean('modal')])>

@unless (request()->boolean('modal'))
<div class="toolbar">
  <span class="title">BSOA Prospectus – Student’s Copy</span>
  <a href="{{ route('prospectus.download') }}">⬇ Download PDF</a>
  <button type="button" id="open-prospectus-preview" aria-haspopup="dialog" aria-controls="prospectus-preview-modal">Modal Preview</button>
  <button type="button" onclick="window.print()">🖨 Print</button>
</div>
@endunless

<div class="scroll">
@include('prospectus_templates.BSOA._document')
</div>

@unless (request()->boolean('modal'))
<dialog class="preview-modal" id="prospectus-preview-modal" aria-labelledby="prospectus-preview-title">
  <div class="toolbar preview-modal-header">
    <span class="title" id="prospectus-preview-title">BSOA Prospectus – Student’s Copy</span>
    <a href="{{ route('prospectus.download') }}">⬇ Download PDF</a>
    <button type="button" onclick="window.print()">🖨 Print</button>
    <button type="button" class="close-button" id="close-prospectus-preview">Close</button>
  </div>
  <div class="preview-modal-scroll">
    @include('prospectus_templates.BSOA._document')
  </div>
</dialog>

<script>
const previewDialog = document.getElementById('prospectus-preview-modal');
document.getElementById('open-prospectus-preview').addEventListener('click', function () {
    previewDialog.showModal();
});
document.getElementById('close-prospectus-preview').addEventListener('click', function () {
    previewDialog.close();
});
</script>
@endunless

@if (!empty($autoprint))
<script>window.addEventListener('load', function () { window.print(); });</script>
@endif
</body></html>
