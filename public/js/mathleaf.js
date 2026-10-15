const MATHLEAF_DATA = {"questions": [{"text": "Suhu di pagi hari −4°C. Pada siang hari suhu naik 9°C. Berapa suhu pada siang hari?", "options": ["−13°C", "−5°C", "5°C", "13°C"], "answer": 2, "explanation": "Suhu awal ditambah kenaikan: −4 + 9 = 5°C."}, {"text": "Hasil dari −6 × (−3) adalah …", "options": ["−18", "18", "−9", "9"], "answer": 1, "explanation": "Perkalian dua bilangan negatif menghasilkan bilangan positif: 6 × 3 = 18."}, {"text": "Rani memakan ¼ bagian pizza dan Dika memakan ½ bagian. Berapa bagian yang dimakan keduanya?", "options": ["⅜", "½", "¾", "1"], "answer": 2, "explanation": "Samakan penyebut: ¼ + ½ = ¼ + ²⁄₄ = ¾."}, {"text": "Pedagang membeli 12 buku seharga Rp8.000 per buku dan menjual semuanya Rp10.000 per buku. Berapa keuntungan total?", "options": ["Rp2.000", "Rp12.000", "Rp20.000", "Rp24.000"], "answer": 3, "explanation": "Untung per buku Rp2.000. Untuk 12 buku: 12 × Rp2.000 = Rp24.000."}, {"text": "Suhu lemari pendingin −8°C. Suhu naik 3°C setiap 10 menit selama 30 menit, lalu turun 5°C. Suhu akhirnya adalah …", "options": ["−4°C", "4°C", "−10°C", "1°C"], "answer": 0, "explanation": "Ada 3 kali kenaikan: −8 + (3 × 3) − 5 = −4°C."}], "steps": [{"text": "Tonton video, temukan hal baru, dan tuliskan pertanyaanmu.", "url": "video"}, {"text": "Baca e-modul dan uji pemahamanmu lewat E-LKPD interaktif.", "url": "latihan"}, {"text": "Berkolaborasi dengan teman untuk memecahkan masalah nyata.", "url": "tugas"}, {"text": "Bagikan hasil, refleksikan proses, dan rayakan perkembanganmu.", "url": "kuis"}], "materials": [{"id": 1, "title": "Bilangan Bulat"}, {"id": 2, "title": "Penjumlahan & Pengurangan"}, {"id": 3, "title": "Perkalian & Pembagian"}, {"id": 4, "title": "Bilangan Pecahan"}, {"id": 5, "title": "Operasi Hitung Pecahan"}, {"id": 6, "title": "Bilangan dalam Kehidupan"}]};
(()=>{'use strict';
const $=s=>document.querySelector(s),$$=s=>Array.from(document.querySelectorAll(s));
const get=(key,def='')=>{try{return JSON.parse(localStorage.getItem('mathleaf_'+key))??def}catch{return def}};
const set=(key,val)=>{try{localStorage.setItem('mathleaf_'+key,JSON.stringify(val))}catch{}};
const text=(el,val)=>{if(el)el.textContent=val};
const escape=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const page=document.body.dataset.page;
const menu=$('#menu-toggle'); if(menu)menu.addEventListener('click',()=>{const nav=$('#public-navigation');const open=nav.classList.toggle('open');menu.setAttribute('aria-expanded',String(open))});
const sidebar=$('#collapse-sidebar');if(sidebar)sidebar.addEventListener('click',()=>$('#app-layout').classList.toggle('sidebar-collapsed'));
const bell=$('#notification-button');if(bell)bell.addEventListener('click',()=>{$('#notification-panel').hidden=!$('#notification-panel').hidden});
const name=get('name','Aurora');$$('[data-student-name]').forEach(el=>text(el,name));text($('#avatar-letter'),String(name).charAt(0).toUpperCase());
$$('[data-stat-score]').forEach(el=>text(el,get('quizScore',85)));
$$('[data-stat-progress]').forEach(el=>text(el,get('completed',false)?'72%':'65%'));
$$('[data-completed-count]').forEach(el=>text(el,get('completed',false)?4:3));
$$('[data-task-count]').forEach(el=>text(el,get('submitted',false)?'9/12':'8/12'));
if($('#dashboard-bar') && get('completed',false)){$('#dashboard-bar .progress-track > div').style.width='100%';text($('[data-progress-label]'),'100%')}

if ($('[data-fpbl]')) {
    $$('[data-step]').forEach(b => {
        b.addEventListener('click', () => {
            const n = Number(b.dataset.step);

            $$('[data-step]').forEach(x => {
                x.classList.toggle('active', x === b);
                x.setAttribute('aria-pressed', String(x === b));
            });

            text($('#step-text'), MATHLEAF_DATA.steps[n].text);

            // Arahkan ke halaman Laravel, bukan HTML biasa
            $('#step-link').href = '/' +
                MATHLEAF_DATA.steps[n].url
                    .replace(/^\/+/, '')
                    .replace(/\.html$/, '');
        });
    });
}

if (page === 'masuk' || page === 'daftar') {
    let role = 'Siswa';

    $$('[data-role]').forEach(btn => {
        btn.addEventListener('click', () => {
            role = btn.dataset.role;

            $$('[data-role]').forEach(x => {
                x.classList.toggle('selected', x === btn);
            });

            if ($('#class-field')) {
                $('#class-field').hidden = role === 'Guru';
            }
        });
    });

    $('#auth-form').addEventListener('submit', e => {
        e.preventDefault();

        if ($('#name') && $('#name').value.trim()) {
            set('name', $('#name').value.trim());
        }

        set('role', role);

        // Redirect sesuai routing Laravel
        if (role === 'Guru') {
            window.location.href = '/guru';
        } else {
            window.location.href = '/dashboard';
        }
    });
}
if(page==='materi'){
 let tab='Semua Materi';const filter=()=>{const query=($('#material-search')?.value||'').toLowerCase();let count=0;$$('.material-item').forEach(item=>{const art=item.querySelector('[data-material-title]');const pr=Number(art.dataset.materialProgress);const ok=art.dataset.materialTitle.includes(query)&&(tab==='Semua Materi'||tab==='Sedang Dipelajari'&&pr>0||tab==='Belum Dimulai'&&pr===0);item.hidden=!ok;if(ok)count++});$('#no-material').hidden=count!==0};
 $$('[data-material-tab]').forEach(b=>b.addEventListener('click',()=>{tab=b.dataset.materialTab;$$('[data-material-tab]').forEach(x=>x.classList.toggle('selected',x===b));filter()}));$('#material-search').addEventListener('input',filter);
}
if(page==='video'){
 const id=Number(new URLSearchParams(location.search).get('materi'))||1;const material=MATHLEAF_DATA.materials.find(x=>x.id===id)||MATHLEAF_DATA.materials[0];text($('#material-heading'),material.title);text($('#modul-panel h2'),material.title);
 const lessons=['Mengenal Bilangan Bulat','Membandingkan Bilangan','Garis Bilangan','Bilangan di Sekitar Kita'];let active=0, playing=false,sec=0,timer=null;
 const update=()=>{text($('#video-timer'),'00:'+String(sec).padStart(2,'0')+' / 01:00');$('#video-bar').style.width=(sec/60*100)+'%';text($('#math-video'),sec<20?'−3 < 0 < 5':sec<40?'−3 + 5 = 2':'−4 × (−2) = 8');text($('#video-explainer'),sec<20?'Bilangan negatif berada di sebelah kiri nol.':sec<40?'Penjumlahan positif bergerak ke kanan pada garis bilangan.':'Dua tanda negatif dalam perkalian menghasilkan positif.')};
 const pause=()=>{playing=false;clearInterval(timer);text($('#play-lesson'),'▶ Putar Pelajaran')};
 $('#play-lesson').addEventListener('click',()=>{playing=!playing;if(!playing){pause();return}text($('#play-lesson'),'Ⅱ Jeda');timer=setInterval(()=>{sec=Math.min(60,sec+1);update();if(sec===60)pause()},1000)});
 const changeTab=(tab)=>{const mod=tab==='modul';$('#modul-panel').hidden=!mod;$('#video-panel').hidden=mod;$$('[data-video-tab]').forEach(x=>x.classList.toggle('selected',x.dataset.videoTab===tab))};
 $$('[data-video-tab]').forEach(b=>b.addEventListener('click',()=>changeTab(b.dataset.videoTab)));$('#open-modul').addEventListener('click',()=>changeTab('modul'));
 $$('[data-lesson]').forEach(b=>b.addEventListener('click',()=>{active=Number(b.dataset.lesson);sec=0;pause();update();text($('#lesson-heading'),lessons[active]);text($('#video-title-secondary'),lessons[active]);$$('[data-lesson]').forEach(x=>x.classList.toggle('selected',x===b));changeTab('video')}));
 if(get('completed',false)) {text($('#video-percent'),'100%');$('#video-progress .progress-track>div').style.width='100%'}
 $('#question-form').addEventListener('submit',e=>{e.preventDefault();if($('#q1').value.trim().length<10||$('#q2').value.trim().length<10){text($('#video-notice'),'Tuliskan dua pertanyaan minimal 10 karakter.');$('#video-notice').hidden=false;return}set('completed',true);text($('#video-notice'),'Pertanyaan tercatat dalam browser demo. Aktivitas selesai!');$('#video-notice').hidden=false;text($('#video-percent'),'100%');$('#video-progress .progress-track>div').style.width='100%'})
}
if(page==='latihan'){
 let choice=null;$$('[data-practice-choice]').forEach(b=>b.addEventListener('click',()=>{choice=Number(b.dataset.practiceChoice);$$('[data-practice-choice]').forEach(x=>x.classList.toggle('selected',x===b))}));
 $('#sort-numbers').addEventListener('click',e=>{const b=e.target.closest('[data-move]');if(!b)return;const card=b.closest('.drag-item');const peer=b.dataset.move==='left'?card.previousElementSibling:card.nextElementSibling;if(peer){if(b.dataset.move==='left')card.parentNode.insertBefore(card,peer);else card.parentNode.insertBefore(peer,card)}});
 $('#check-practice').addEventListener('click',()=>{const a=[choice===2,$('[data-practice-answer="1"]').value.trim()==='-12',$('[data-practice-answer="2"]').value.replace(/\s/g,'')==='3/4',$('[data-practice-answer="3"]').value.trim()==='24000',$$('#sort-numbers .drag-item').map(el=>Number(el.dataset.num)).join(',')==='-5,-2,0,1,3'];const feedback=['−3 + 5 = 2.','−4 − (2 × 4) = −12°C.','½ + ¼ = ¾.','12 × (10.000 − 8.000) = Rp24.000.','Urutan benar: −5, −2, 0, 1, 3.'];a.forEach((ok,i)=>{const el=$(`[data-feedback="${i}"]`);el.hidden=false;el.classList.toggle('error-notice',!ok);text(el,(ok?'Benar! 🌱 ':'Coba lagi ya. ')+feedback[i])});let score=a.filter(Boolean).length*20;text($('#practice-result'),`Skor latihan: ${score}/100 · ${a.filter(Boolean).length} dari 5 benar.`);$('#practice-result').hidden=false});
}
if(page==='kuis'){
 let index=0,ans=Array(MATHLEAF_DATA.questions.length).fill(null);const Q=MATHLEAF_DATA.questions;
 const render=()=>{const q=Q[index];text($('#quiz-counter'),`Soal ${index+1} dari ${Q.length}`);text($('#quiz-question'),q.text);$('#quiz-progress').style.width=((index+1)/Q.length*100)+'%';$('#quiz-options').innerHTML=q.options.map((opt,i)=>`<button type="button" data-answer="${i}" class="quiz-option btn ${ans[index]===i?'selected':''}"><span class="option-letter">${'ABCD'[i]}</span>${escape(opt)}</button>`).join('');$('#quiz-dots').innerHTML=Q.map((_,i)=>`<button type="button" data-jump="${i}" class="btn btn-outline ${i===index?'selected':''}">${i+1}</button>`).join('');$('#quiz-prev').disabled=index===0;text($('#quiz-next'),index===Q.length-1?'Selesaikan Kuis':'Selanjutnya →')};
 $('#quiz-start-button').addEventListener('click',()=>{$('#quiz-start').hidden=true;$('#quiz-current').hidden=false;render()});
 $('#quiz-options').addEventListener('click',e=>{let b=e.target.closest('[data-answer]');if(b){ans[index]=Number(b.dataset.answer);render()}});
 $('#quiz-dots').addEventListener('click',e=>{let b=e.target.closest('[data-jump]');if(b){index=Number(b.dataset.jump);render()}});
 $('#quiz-prev').addEventListener('click',()=>{index=Math.max(0,index-1);render()});
 const finish=()=>{const correct=ans.filter((a,i)=>a===Q[i].answer).length;const score=correct*20;set('quizScore',score);$('#quiz-current').hidden=true;$('#quiz-results').hidden=false;text($('#quiz-score'),score);text($('#quiz-summary'),`${correct} dari ${Q.length} jawaban benar`);$('#quiz-review').innerHTML=Q.map((q,i)=>`<div class="review-row"><h3>${i+1}. ${escape(q.text)}</h3><p>Jawabanmu: ${ans[i]===null?'Belum dijawab':escape(q.options[ans[i]])} · Jawaban benar: ${escape(q.options[q.answer])}</p><p>${escape(q.explanation)}</p></div>`).join('')};
 $('#quiz-next').addEventListener('click',()=>{if(index<Q.length-1){index++;render()}else finish()});
 $('#quiz-restart').addEventListener('click',()=>{index=0;ans=Array(Q.length).fill(null);$('#quiz-results').hidden=true;$('#quiz-start').hidden=false});
}
if(page==='forum'){
 const modal=$('#forum-modal');const topics=$('#forum-topics');
 const item=(title,body)=>{const art=document.createElement('article');art.className='thread';const h=document.createElement('h3'),p=document.createElement('p');text(h,title);text(p,body);art.append(h,p);topics.prepend(art)};
 const stored=get('forumTopics',[]);stored.forEach(t=>item(t.title,t.body));text($('#topic-count'),(3+stored.length)+' topik aktif');
 $('#new-topic-btn').addEventListener('click',()=>modal.hidden=false);$$('[data-close-modal]').forEach(el=>el.addEventListener('click',()=>el.closest('.modal-backdrop').hidden=true));
 $('#forum-new-form').addEventListener('submit',e=>{e.preventDefault();const t={title:$('#topic-title').value.trim(),body:$('#topic-body').value.trim()};if(!t.title||!t.body)return;const all=get('forumTopics',[]);all.unshift(t);set('forumTopics',all);item(t.title,t.body);text($('#topic-count'),(3+all.length)+' topik aktif');modal.hidden=true;e.target.reset()});
 $('#forum-reply-form').addEventListener('submit',e=>{e.preventDefault();text($('#forum-reply-notice'),'Balasan demo kamu: '+$('#forum-reply').value);$('#forum-reply-notice').hidden=false;e.target.reset()});
}
if(page==='tugas'){
 const existing=get('submitted','');if(existing){text($('#task-status'),'Tercatat (demo)');$('#task-status').classList.remove('yellow')}
 $$('[data-task-tab]').forEach(b=>b.addEventListener('click',()=>{const ind=b.dataset.taskTab==='individu';$$('[data-task-tab]').forEach(x=>x.classList.toggle('selected',x===b));text($('#task-badge'),ind?'Tugas Individu':'Kelompok 3 · Daun Cerdas');text($('#task-title'),ind?'Menghitung Untung-Rugi Toko Buku':'Menyelidiki Perubahan Suhu');text($('#task-desc'),ind?'Hitung keuntungan sebuah toko buku yang membeli 12 buku seharga Rp8.000 dan menjualnya Rp10.000 per buku. Jelaskan langkah perhitungan.':'Suhu kota A pada pukul 06.00 adalah −3°C, pukul 12.00 adalah 8°C, dan pukul 18.00 adalah 2°C. Buat garis bilangan dan bandingkan perubahan suhunya dengan kota B.')}));
 $('#submit-task').addEventListener('click',()=>{const f=$('#task-file').files[0];const n=$('#task-notice');n.hidden=false;if(!f){text(n,'Pilih berkas terlebih dahulu.');return}if(f.size>10*1024*1024){text(n,'Ukuran file maksimal 10 MB.');return}set('submitted',f.name);text(n,'Nama file “'+f.name+'” tersimpan sebagai status demo di browser. File belum dikirim ke server.');text($('#task-status'),'Tercatat (demo)');$('#task-status').classList.remove('yellow')});
 $('#group-form').addEventListener('submit',e=>{e.preventDefault();const d=document.createElement('div');d.className='reply';d.style.marginLeft='0';const b=document.createElement('b'),p=document.createElement('p');text(b,name);text(p,$('#group-input').value);d.append(b,p);$('#group-messages').append(d);e.target.reset()});
}
if(page==='guru'){
 const tabs=['Monitoring Siswa','Manajemen Konten','Pengumpulan Tugas'];const switchTab=i=>{tabs.forEach((t,j)=>$('#teacher-panel-'+j).hidden=j!==i);$$('[data-teacher-tab]').forEach(b=>b.classList.toggle('selected',Number(b.dataset.teacherTab)===i));text($('#teacher-tab-heading'),tabs[i])};
 $$('[data-teacher-tab]').forEach(b=>b.addEventListener('click',()=>switchTab(Number(b.dataset.teacherTab))));
 $('#teacher-new').addEventListener('click',()=>$('#teacher-modal').hidden=false);$$('[data-close-modal]').forEach(b=>b.addEventListener('click',()=>b.closest('.modal-backdrop').hidden=true));
 $('#teacher-form').addEventListener('submit',e=>{e.preventDefault();const tr=document.createElement('tr'),n=$('#activity-name').value,kind=$('#activity-kind').value;tr.innerHTML='<td></td><td></td><td><span class="badge">Draf</span></td><td><button type="button" class="btn btn-outline" data-toggle-publish>Publikasikan</button></td>';tr.children[0].textContent=n;tr.children[1].textContent=kind;$('#teacher-content').prepend(tr);$('#teacher-modal').hidden=true;switchTab(1);e.target.reset();text($('#teacher-notice'),'Aktivitas dibuat sebagai draf demo (tidak tersimpan di server).');$('#teacher-notice').hidden=false});
 $('#teacher-content').addEventListener('click',e=>{const b=e.target.closest('[data-toggle-publish]');if(!b)return;const published=b.textContent==='Publikasikan';text(b,published?'Jadikan Draf':'Publikasikan');text(b.closest('tr').querySelector('.badge'),published?'Dipublikasikan':'Draf')});
 let selected=null;$$('[data-grade-student]').forEach(b=>b.addEventListener('click',()=>{selected=b;text($('#grade-name'),b.dataset.gradeStudent);$('#grade-modal').hidden=false}));
 $('#grade-form').addEventListener('submit',e=>{e.preventDefault();text(selected.closest('tr').querySelector('.grade-state'),'Nilai '+$('#grade-number').value);$('#grade-modal').hidden=true;$('#grade-form').reset();text($('#teacher-notice'),'Penilaian tercatat dalam tampilan demo.');$('#teacher-notice').hidden=false});
}
})();
