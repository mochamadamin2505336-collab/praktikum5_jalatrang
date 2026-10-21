@extends('layouts.app')

@section('title', 'Berita & Informasi')

@section('content')
<div class="row">

    <div class="col-md-3 mb-4">
        <div class="filter-box">
            <div class="filter-header">
                <i class="bi bi-funnel-fill me-1"></i> Filter Berita
            </div>
            <div class="filter-body">
                <form method="GET" action="{{ route('berita.index') }}">
                    <label class="form-label fw-semibold">Kata Kunci</label>
                    <input type="text" name="kata_kunci" class="form-control mb-3"
                           value="{{ request('kata_kunci') }}" placeholder="Cari berita...">

                    <label class="form-label fw-semibold">Kategori</label>
                  <select name="kategori" class="form-select mb-3">
     <option value="">Semua Kategori</option>
    @foreach ($kategoris as $k)
        <option value="{{ $k->id }}" @selected(request('kategori') == $k->id)>{{ $k->nama }}</option>
    @endforeach
</select>


                    <button type="submit" class="btn btn-primary w-100 mb-2">Cari</button>
                    <a href="{{ route('berita.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </form>
            </div>
        </div>

        <a href="{{ route('berita.create') }}" class="btn btn-success w-100 mt-3">+ Tambah Berita</a>
    </div>

    <div class="col-md-9">
        <div class="row">
            @forelse ($beritas as $berita)
                <div class="col-md-4 mb-4">
                    <div class="card card-berita h-100">
                        <img src="{{ $berita->gambar ? asset('storage/'.$berita->gambar) : 'https://placehold.co/400x220?text=Berita+Desa' }}"
                             class="card-img-top" style="height:180px; object-fit:cover;">

                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge badge-kategori text-white">{{ $berita->kategori->nama }}</span>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i> {{ $berita->created_at->format('d M Y') }}
                                </small>
                            </div>

                            <h6>
                                <a href="{{ route('berita.show', $berita) }}" class="text-decoration-none text-dark fw-bold">
                                    {{ Str::limit($berita->judul, 60) }}
                                </a>
                            </h6>

                            <p class="text-muted small flex-grow-1">{{ Str::limit($berita->isi, 80) }}</p>

                        <div class="mb-2">
                            @foreach ($berita->tags as $tag)
                            <a href="{{ route('berita.index', ['tag' => $tag->id]) }}"
                            class="tag-pill text-decoration-none">#{{ $tag->nama }}</a>
                            @endforeach
                        </div>


                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small class="text-muted"><i class="bi bi-eye"></i> {{ $berita->dilihat }}</small>
                                <a href="{{ route('berita.show', $berita) }}" class="btn btn-sm btn-baca">
                                    Baca <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>

                            <div class="mt-2 pt-2 border-top d-flex gap-2">
                                <a href="{{ route('berita.edit', $berita) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                <form action="{{ route('berita.destroy', $berita) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus berita ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Berita tidak ditemukan.</p>
            @endforelse
        </div>

        @if ($beritas->total() > 0)
            <p class="text-muted small">
                Menampilkan {{ $beritas->firstItem() }} - {{ $beritas->lastItem() }} dari {{ $beritas->total() }} hasil
            </p>
        @endif

        {{ $beritas->links() }}
    </div>
</div>
@endsection