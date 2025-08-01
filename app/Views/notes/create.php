<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<form action="/notes/store" method="post">
    <div class="mb-3">
        <label for="title" class="form-label">Judul</label>
        <input type="text" class="form-control" name="title" required>
    </div>
    <div class="mb-3">
        <label for="content" class="form-label">Isi Catatan</label>
        <textarea name="content" class="form-control" rows="5" required></textarea>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="/notes" class="btn btn-secondary">Kembali</a>
</form>

<?= $this->endSection() ?>
