<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<form action="/notes/update/<?= $note['id'] ?>" method="post">
    <div class="mb-3">
        <label for="title" class="form-label">Judul</label>
        <input type="text" class="form-control" name="title" value="<?= esc($note['title']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="content" class="form-label">Isi Catatan</label>
        <textarea name="content" class="form-control" rows="5" required><?= esc($note['content']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="/notes" class="btn btn-secondary">Kembali</a>
</form>

<?= $this->endSection() ?>
