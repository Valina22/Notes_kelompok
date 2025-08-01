<?php

namespace App\Controllers;

use App\Models\NoteModel;

class Notes extends BaseController
{
    protected $noteModel;

    public function __construct()
    {
        $this->noteModel = new NoteModel();
    }

    public function index()
{
    $keyword = $this->request->getGet('keyword');
    
    if ($keyword) {
        $data['notes'] = $this->noteModel
            ->like('title', $keyword)
            ->orLike('content', $keyword)
            ->findAll();
    } else {
        $data['notes'] = $this->noteModel->findAll();
    }

    $data['title'] = 'Catatan Harian';
    return view('notes/index', $data);
}

    public function create()
    {
        return view('notes/create');
    }

    public function store()
    {
        $this->noteModel->save([
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
        ]);
        return redirect()->to('/notes');
    }

    public function edit($id)
    {
        $data['note'] = $this->noteModel->find($id);
        return view('notes/edit', $data);
    }

    public function update($id)
    {
        $this->noteModel->update($id, [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
        ]);
        return redirect()->to('/notes');
    }

    public function delete($id)
    {
        $this->noteModel->delete($id);
        return redirect()->to('/notes');
    }
}
