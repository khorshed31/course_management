<script>
(() => {

  // ---------- Preview helpers ----------
  function isYouTube(url) {
    return /(?:youtube\.com\/watch\?v=|youtu\.be\/)/i.test(url || '');
  }
  function ytEmbed(url) {
    try {
      const idMatch = url.match(/(?:v=|\/)([0-9A-Za-z_-]{11})(?:[?&]|$)/);
      const id = idMatch ? idMatch[1] : null;
      return id ? `https://www.youtube.com/embed/${id}` : null;
    } catch { return null; }
  }
  function isVimeo(url) {
    return /vimeo\.com\/\d+/i.test(url || '');
  }
  function vimeoEmbed(url) {
    try {
      const idMatch = url.match(/vimeo\.com\/(\d+)/);
      const id = idMatch ? idMatch[1] : null;
      return id ? `https://player.vimeo.com/video/${id}` : null;
    } catch { return null; }
  }
  function isImageMime(mime) {
    return (mime || '').toLowerCase().startsWith('image/');
  }
  function isPdfMime(mime) {
    return (mime || '').toLowerCase() === 'application/pdf';
  }
  function isDirectVideoUrl(url) {
    return /\.(mp4|webm|ogg|mov|mkv|avi)(\?.*)?$/i.test(url || '');
  }
  function escHtml(str) {
    const div = document.createElement('div');
    div.innerText = str ?? '';
    return div.innerHTML;
  }

  document.getElementById('previewModal').addEventListener('hidden.bs.modal', () => {
    const contEl = document.getElementById('previewContainer');
    const metaEl = document.getElementById('previewMeta');
    contEl.classList.add('ratio','ratio-16x9');
    contEl.innerHTML = '';
    metaEl.innerHTML = '';
  });

  // ---------------- Global vars ----------------
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  const courseId = document.querySelector('[data-course-id]')?.getAttribute('data-course-id');

  const chapterModalEl = document.getElementById('chapterModal');
  const lessonModalEl = document.getElementById('lessonModal');
  let currentChapterId = null, currentChapterTitle = null;

  // Dropzones
  let dzLessonFile = null;
  let dzLessonVideo = null;

  // ---------------- Chapters DT ----------------
  const dtChapters = new DataTable('#tblChapters', {
    ajax: { url: `{{ url('admin/courses') }}/${courseId}/chapters`, dataSrc: 'data' },
    processing: true,
    serverSide: false,
    columns: [
      { 
        data: null, width: 60,
        render: (data, type, row, meta) => meta.row + 1 // SL
      },
      { data: 'title' , width: 100},
      { data: 'sort_order', width: 80 },
      { data: 'status', width: 90, render:(s,t,row)=> row.status_bool ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' },
      { data: 'lessons_count', width: 90 },
      { data: null, width: 150, orderable:false, searchable:false, render:(row)=> `
        <button class="btn btn-sm btn-outline-secondary me-1 btn-open" data-id="${row.id}" data-title="${row.title}">
          <i class="bi bi-list-ul"></i>
        </button>
        <button class="btn btn-sm btn-outline-primary me-1 btn-edit" data-id="${row.id}">
          <i class="bi bi-pencil-square"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${row.id}" data-title="${row.title}">
          <i class="bi bi-trash"></i>
        </button>` }
    ]
  });

  // ---------------- Lessons DT ----------------
  const dtLessons = new DataTable('#tblLessons', {
    ajax: (d, cb) => {
      if (!currentChapterId) return cb({data:[]});
      fetch(`{{ url('admin/courses') }}/${courseId}/chapters/${currentChapterId}/lessons`, {
        credentials: 'same-origin',
        headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}
      }).then(r=>r.json()).then(cb);
    },
    processing: true,
    serverSide: false,
    columns: [
      { 
        data: null, width: 60,
        render: (data, type, row, meta) => meta.row + 1 // SL
      },
      { data: 'title' , width: 140 },
      { data: 'type', width: 80 },
      { data: 'duration', width: 90, render: v => v ? v + 's' : '-' },
      { data: null, width: 80, render: (row) => row.sort_order ?? '-' },
      { data: 'toils', width: 80, render: v => (v ?? '-') },
      { data: 'rounds', width: 140, render: v => (v || '-') },
      { data: 'notes',  width: 200, render: v => v ? (String(v).length>80 ? String(v).slice(0,77)+'…' : v) : '-' },
      { data: 'others', width: 120, render: v => v || '-' },
      { data: 'status', width: 90, render:(s,t,row)=> row.status_bool ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' },
      { data: null, width: 210, orderable:false, searchable:false, 
        render:(row)=> `
          <button class="btn btn-sm btn-outline-secondary me-1 btn-preview" data-id="${row.id}">
            <i class="bi bi-eye"></i>
          </button>
          <button class="btn btn-sm btn-outline-primary me-1 btn-edit-lesson" data-id="${row.id}">
            <i class="bi bi-pencil-square"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger btn-delete-lesson" data-id="${row.id}" data-title="${row.title}">
            <i class="bi bi-trash"></i>
          </button>`
      }
    ]
  });

  // ---------------- Chapter actions ----------------
  document.getElementById('tblChapters').addEventListener('click', (e) => {
    const openBtn = e.target.closest('.btn-open');
    const editBtn = e.target.closest('.btn-edit');
    const delBtn  = e.target.closest('.btn-delete');
    if (!openBtn && !editBtn && !delBtn) return;

    const id = (openBtn || editBtn || delBtn).getAttribute('data-id');
    const row = dtChapters.rows().data().toArray().find(r => r.id == id);

    if (openBtn) {
      currentChapterId = row.id;
      currentChapterTitle = row.title;
      document.getElementById('activeChapterTitle').innerText = currentChapterTitle;
      document.getElementById('btnAddLesson').disabled = false;
      dtLessons.ajax.reload(null, false);
    }

    if (editBtn) {
      document.getElementById('chapter_id').value = row.id;
      document.getElementById('chapter_title').value = row.title;
      document.getElementById('chapter_sort_order').value = row.sort_order ?? 1;
      document.getElementById('chapter_status').value = row.status_bool ? '1':'0';
      document.getElementById('chapter_method').value = 'PUT';
      document.getElementById('chapterModalTitle').innerText = 'Edit Chapter';
      new bootstrap.Modal(chapterModalEl).show();
    }

    if (delBtn) {
      if (!confirm(`Delete chapter "${row.title}"? All lessons under it will also be deleted.`)) return;
      fetch(`{{ url('admin/courses') }}/${courseId}/chapters/${row.id}`, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: {'X-CSRF-TOKEN': token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
      }).then(r => {
        if (!r.ok) throw new Error();
        dtChapters.ajax.reload(null, false);
        if (currentChapterId == row.id) {
          currentChapterId = null;
          document.getElementById('activeChapterTitle').innerText = 'None';
          document.getElementById('btnAddLesson').disabled = true;
          dtLessons.clear().draw();
        }
      }).catch(()=> alert('Delete failed'));
    }
  });

  document.getElementById('btnAddChapter').addEventListener('click', () => {
    document.getElementById('chapterForm').reset();
    document.getElementById('chapter_id').value = '';
    document.getElementById('chapter_sort_order').value = '1';
    document.getElementById('chapter_status').value = '1';
    document.getElementById('chapter_method').value = 'POST';
    document.getElementById('chapterModalTitle').innerText = 'Add Chapter';
    new bootstrap.Modal(chapterModalEl).show();
  });

  document.getElementById('chapterForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('chapter_id').value;
    const method = document.getElementById('chapter_method').value;
    const fd = new FormData();
    fd.append('title', document.getElementById('chapter_title').value.trim());
    fd.append('sort_order', document.getElementById('chapter_sort_order').value || '1');
    fd.append('status', document.getElementById('chapter_status').value);

    const url = method === 'PUT'
      ? `{{ url('admin/courses') }}/${courseId}/chapters/${id}`
      : `{{ url('admin/courses') }}/${courseId}/chapters`;

    const res = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {'X-CSRF-TOKEN': token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
      body: (()=>{ if (method==='PUT') fd.append('_method','PUT'); return fd; })()
    });

    if (!res.ok) { alert('Save failed'); return; }
    bootstrap.Modal.getInstance(chapterModalEl).hide();
    dtChapters.ajax.reload(null, false);
  });

  // ---------------- Lessons UI helpers ----------------
  function getVideoSource(){
    return document.getElementById('videoSrcUpload').checked ? 'upload' : 'url';
  }
  function setVideoSourceUI(src){
    const urlRow = document.getElementById('videoUrlRow');
    const uploadRow = document.getElementById('videoUploadRow');
    const provider = document.getElementById('lesson_video_provider');
    if (src === 'upload') {
      urlRow.classList.add('d-none');
      uploadRow.classList.remove('d-none');
      provider.value = 'local';
    } else {
      urlRow.classList.remove('d-none');
      uploadRow.classList.add('d-none');
    }
  }
  function toggleLessonFields() {
    const t = document.getElementById('lesson_type').value;
    const isVideo = (t === 'video');
    document.getElementById('videoFields').classList.toggle('d-none', !isVideo);
    document.getElementById('fileFields').classList.toggle('d-none', t !== 'file');
    document.getElementById('textFields').classList.toggle('d-none', t !== 'text');
    if (isVideo) setVideoSourceUI(getVideoSource());
  }
  document.getElementById('lesson_type').addEventListener('change', toggleLessonFields);
  document.getElementById('videoSrcUrl').addEventListener('change', ()=> setVideoSourceUI('url'));
  document.getElementById('videoSrcUpload').addEventListener('change', ()=> setVideoSourceUI('upload'));

  // Dropzones
  Dropzone.autoDiscover = false;
  function buildLessonFileDZ() {
    if (dzLessonFile) dzLessonFile.destroy();
    dzLessonFile = new Dropzone("#lessonFileDropzone", {
      url: "#",
      autoProcessQueue: false,
      maxFiles: 1,
      maxFilesize: 10,
      addRemoveLinks: true,
      paramName: 'file'
    });
    dzLessonFile.on('maxfilesexceeded', function(file){ this.removeAllFiles(); this.addFile(file); });
  }
  function buildLessonVideoDZ() {
    if (dzLessonVideo) dzLessonVideo.destroy();
    dzLessonVideo = new Dropzone("#lessonVideoDropzone", {
      url: "#",
      autoProcessQueue: false,
      maxFiles: 1,
      maxFilesize: 500, // MB
      addRemoveLinks: true,
      acceptedFiles: "video/*",
      paramName: 'video_file'
    });
    dzLessonVideo.on('maxfilesexceeded', function(file){ this.removeAllFiles(); this.addFile(file); });
  }
  buildLessonFileDZ();
  buildLessonVideoDZ();

  // Add Lesson
  document.getElementById('btnAddLesson').addEventListener('click', () => {
    if (!currentChapterId) return;
    document.getElementById('lessonForm').reset();
    document.getElementById('lesson_id').value = '';
    document.getElementById('lesson_method').value = 'POST';
    document.getElementById('lesson_sort_order').value = '1';
    document.getElementById('lesson_status').value = '1';
    document.getElementById('lesson_type').value = 'text';
    document.getElementById('videoSrcUrl').checked = true;
    document.getElementById('videoSrcUpload').checked = false;
    document.getElementById('lesson_notes').value  = '';
    document.getElementById('lesson_others').value = '';
    dzLessonFile.removeAllFiles(true);
    dzLessonVideo.removeAllFiles(true);
    toggleLessonFields();
    // NEW: reset Toils & Rounds
    const toilsEl = document.getElementById('lesson_toils');
    const roundsEl = document.getElementById('lesson_rounds');
    if (toilsEl) toilsEl.value = '';
    if (roundsEl) roundsEl.value = '';

    document.getElementById('lessonModalTitle').innerText = `Add Lesson (Chapter: ${currentChapterTitle})`;
    new bootstrap.Modal(lessonModalEl).show();
  });

  // Edit/Delete lesson
  document.getElementById('tblLessons').addEventListener('click', (e) => {
    const previewBtn = e.target.closest('.btn-preview');
    const editBtn    = e.target.closest('.btn-edit-lesson');
    const delBtn     = e.target.closest('.btn-delete-lesson');
    if (!previewBtn && !editBtn && !delBtn) return;

    const id  = (previewBtn || editBtn || delBtn).getAttribute('data-id');
    const row = dtLessons.rows().data().toArray().find(r => r.id == id);

    // ----- PREVIEW -----
    if (previewBtn) {
      const modalEl = document.getElementById('previewModal');
      const titleEl = document.getElementById('previewTitle');
      const contEl  = document.getElementById('previewContainer');
      const metaEl  = document.getElementById('previewMeta');

      titleEl.innerText = `Preview: ${row.title}`;
      metaEl.innerHTML  = '';
      contEl.innerHTML  = '';

      if (row.type === 'video') {
        if (row.video_file_url) {
          contEl.innerHTML = `
            <video controls playsinline style="width:100%; height:100%; object-fit:contain;">
              <source src="${row.video_file_url}">
              Your browser does not support HTML5 video.
            </video>`;
          metaEl.innerText = 'Source: uploaded video';
        } else if (row.video_url) {
          let embed = null;
          if (isYouTube(row.video_url)) embed = ytEmbed(row.video_url);
          else if (isVimeo(row.video_url)) embed = vimeoEmbed(row.video_url);

          if (embed) {
            contEl.innerHTML = `
              <iframe src="${embed}" allowfullscreen allow="autoplay; encrypted-media" style="border:0;"></iframe>`;
            metaEl.innerText = 'Source: embedded video';
          } else if (isDirectVideoUrl(row.video_url)) {
            contEl.innerHTML = `
              <video controls playsinline style="width:100%; height:100%; object-fit:contain;">
                <source src="${row.video_url}">
              </video>`;
            metaEl.innerText = 'Source: direct video URL';
          } else {
            contEl.innerHTML = `<div class="d-flex align-items-center justify-content-center">
              <div class="text-center">
                <div class="mb-2">Cannot embed this URL.</div>
                <a class="btn btn-sm btn-primary" href="${row.video_url}" target="_blank" rel="noopener">Open in new tab</a>
              </div>
            </div>`;
            metaEl.innerText = 'Source: external URL';
          }
        } else {
          contEl.innerHTML = `<div class="d-flex align-items-center justify-content-center text-muted">No video available.</div>`;
        }
      } else if (row.type === 'file') {
        if (row.file_url) {
          if (isPdfMime(row.mime_type)) {
            contEl.innerHTML = `<iframe src="${row.file_url}#toolbar=1" style="border:0;"></iframe>`;
            metaEl.innerText = 'Previewing PDF';
          } else if (isImageMime(row.mime_type)) {
            contEl.classList.remove('ratio','ratio-16x9');
            contEl.innerHTML = `<img src="${row.file_url}" class="img-fluid d-block mx-auto" alt="Preview">`;
            metaEl.innerText = 'Previewing image';
          } else {
            contEl.innerHTML = `<div class="d-flex align-items-center justify-content-center text-center">
              <div>
                <div class="mb-2">This file type cannot be previewed.</div>
                <a class="btn btn-sm btn-primary" href="${row.file_url}" target="_blank" rel="noopener">Download / Open</a>
              </div>
            </div>`;
            metaEl.innerText = (row.mime_type || 'Unknown type');
          }
        } else {
          contEl.innerHTML = `<div class="d-flex align-items-center justify-content-center text-muted">No file attached.</div>`;
        }
      } else {
        contEl.classList.remove('ratio','ratio-16x9');
        contEl.innerHTML = `<div class="p-3" style="max-height:70vh; overflow:auto; white-space:pre-wrap;">${escHtml(row.content_text || '')}</div>`;
        metaEl.innerText = 'Text lesson';
      }

      new bootstrap.Modal(modalEl).show();
      return;
    }

    if (editBtn) {
      document.getElementById('lesson_id').value = row.id;
      document.getElementById('lesson_title').value = row.title;
      document.getElementById('lesson_type').value = row.type;
      document.getElementById('lesson_sort_order').value = row.sort_order ?? 1;
      document.getElementById('lesson_status').value = row.status_bool ? '1':'0';
      document.getElementById('lesson_duration').value = row.duration ?? '';
      document.getElementById('lesson_content_text').value = row.type === 'text' ? (row.content_text ?? '') : '';

      document.getElementById('lesson_video_provider').value = row.video_provider ?? '';
      document.getElementById('lesson_video_url').value = row.video_url ?? '';
      document.getElementById('lesson_notes').value  = row.notes  ?? '';
      document.getElementById('lesson_others').value = row.others ?? '';

      // NEW: populate Toils & Rounds
      const toilsEl = document.getElementById('lesson_toils');
      const roundsEl = document.getElementById('lesson_rounds');
      if (toilsEl) toilsEl.value = row.toils ?? '';
      if (roundsEl) roundsEl.value = row.rounds ?? '';

      dzLessonFile.removeAllFiles(true);
      dzLessonVideo.removeAllFiles(true);

      if (row.type === 'video') {
        if (row.video_file_url) {
          document.getElementById('videoSrcUpload').checked = true;
          document.getElementById('videoSrcUrl').checked = false;
        } else {
          document.getElementById('videoSrcUrl').checked = true;
          document.getElementById('videoSrcUpload').checked = false;
        }
      } else {
        document.getElementById('videoSrcUrl').checked = true;
        document.getElementById('videoSrcUpload').checked = false;
      }

      toggleLessonFields();
      document.getElementById('lesson_method').value = 'PUT';
      document.getElementById('lessonModalTitle').innerText = `Edit Lesson (Chapter: ${currentChapterTitle})`;
      new bootstrap.Modal(lessonModalEl).show();
    }

    if (delBtn) {
      if (!confirm(`Delete lesson "${row.title}"?`)) return;
      fetch(`{{ url('admin/courses') }}/${courseId}/chapters/${currentChapterId}/lessons/${row.id}`, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: {'X-CSRF-TOKEN': token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
      }).then(r => {
        if (!r.ok) throw new Error();
        dtLessons.ajax.reload(null, false);
        dtChapters.ajax.reload(null, false);
      }).catch(()=> alert('Delete failed'));
    }
  });

  // Save Lesson
  document.getElementById('lessonForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!currentChapterId) return;

    const method = document.getElementById('lesson_method').value;
    const id = document.getElementById('lesson_id').value;

    const fd = new FormData();
    fd.append('title', document.getElementById('lesson_title').value.trim());
    fd.append('type', document.getElementById('lesson_type').value);
    fd.append('sort_order', document.getElementById('lesson_sort_order').value || '1');
    fd.append('status', document.getElementById('lesson_status').value);
    fd.append('duration_seconds', document.getElementById('lesson_duration').value || '');
    fd.append('video_provider', document.getElementById('lesson_video_provider').value || '');
    fd.append('video_url', document.getElementById('lesson_video_url').value || '');
    fd.append('content_text', document.getElementById('lesson_content_text').value || '');
    fd.append('notes',  document.getElementById('lesson_notes').value  || '');
    fd.append('others', document.getElementById('lesson_others').value || '');  

    // NEW: send Toils & Rounds
    const toilsEl = document.getElementById('lesson_toils');
    const roundsEl = document.getElementById('lesson_rounds');
    fd.append('toils',  (toilsEl?.value || '').trim());
    fd.append('rounds', (roundsEl?.value || '').trim());

    if (document.getElementById('lesson_type').value === 'file') {
      if (dzLessonFile.getAcceptedFiles().length) {
        const f = dzLessonFile.getAcceptedFiles()[0];
        fd.append('file', f, f.name);
      }
    }

    if (document.getElementById('lesson_type').value === 'video') {
      const source = document.getElementById('videoSrcUpload').checked ? 'upload' : 'url';
      if (source === 'upload') {
        fd.set('video_provider', 'local');
        fd.set('video_url', '');
        if (dzLessonVideo.getAcceptedFiles().length) {
          const vf = dzLessonVideo.getAcceptedFiles()[0];
          fd.append('video_file', vf, vf.name);
        }
      }
    }

    const url = method === 'PUT'
      ? `{{ url('admin/courses') }}/${courseId}/chapters/${currentChapterId}/lessons/${id}`
      : `{{ url('admin/courses') }}/${courseId}/chapters/${currentChapterId}/lessons`;

    const res = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {'X-CSRF-TOKEN': token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
      body: (()=>{ if (method==='PUT') fd.append('_method','PUT'); return fd; })()
    });

    if (!res.ok) {
      const data = await res.json().catch(()=> ({}));
      alert(data?.message || 'Save failed');
      return;
    }
    bootstrap.Modal.getInstance(lessonModalEl).hide();
    dtLessons.ajax.reload(null, false);
    dtChapters.ajax.reload(null, false);
  });
})();   
</script>
