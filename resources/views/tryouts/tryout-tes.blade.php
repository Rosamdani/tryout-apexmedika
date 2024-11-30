@extends('layouts.index')

@section('title', 'Try Out')
@section('style')
<style>
    #question-grid .dijawab {
        background-color: #CA0000FF !important;
        color: #FFFFFFFF !important;
    }

    #question-grid .belum_dijawab {
        background-color: #E6E6E6FF !important;
        color: #000000FF !important;
    }

    #question-grid .ragu-ragu {
        background-color: #FFD700FF !important;
        color: #000000FF !important;
    }

    #question-grid .active {
        background-color: #FFF19EFF;
        border: solid 2px #FFB300FF !important;
        color: #000000FF;
    }
</style>
@endsection
@section('content')
<div class="row mb-3">
    <div class="col-12 col-md-9 mb-3">
        <div class="card border-0 shadow-sm" id="question-content">

        </div>
    </div>
    <div class="col-12 col-md-3">
        <button class="btn btn-primary w-100 mb-2 py-2">Selesai</button>
        <button class="btn btn-outline-primary w-100 mb-2 py-2">Pause</button>
        <div class="card border-0 mb-2 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-5">
                        <small class="mb-0">Sisa Waktu</small>
                    </div>
                    <div class="col-7">
                        <div class="row text-center mx-1 justify-content-evenly">
                            <div style="max-width: 35px;" class="col-4 card p-1 border-0 alert alert-secondary mb-0">
                                <small id="hours" class="m-1" style="font-size: 12px;">00</small>
                            </div>
                            <div style="max-width: 35px;" class="col-4 card p-1 border-0 alert alert-secondary mb-0">
                                <small id="minutes" class="m-1" style="font-size: 12px;">00</small>
                            </div>
                            <div style="max-width: 35px;" class="col-4 card p-1 border-0 alert alert-secondary mb-0">
                                <small id="seconds" class="m-1" style="font-size: 12px;">00</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-2">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <small class="mb-0" style="font-size: 12px;">Pertanyaan sudah dijawab</small>
                    <p class="fw-bold mb-0"><span id="jumlah_soal_dijawab"></span>/ <span id="jumlah_soal"></span></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div id="question-grid-header" class="d-flex gap-3 rounded mb-3 p-2" style="background-color: #E6E6E6FF;">
                <button id="grid_soal_semua" class="btn grid_ text-secondary btn-light">Semua soal</button>
                <button id="grid_soal_dijawab" class="btn grid_.dijawab text-secondary">Soal Sudah Dijawab</button>
                <button id="grid_soal_ragu-ragu" class="btn grid_.ragu-ragu text-secondary ">Soal Masih
                    Ragu-ragu</button>
                <button id="grid_soal_belum_dijawab" class="btn grid_.belum_dijawab text-secondary ">Soal Belum
                    Dijawab</button>
            </div>
            <div id="question-grid"
                style="display: grid; grid-template-columns: repeat(20, minmax(0, 1fr)); gap: 0.5rem;">

            </div>
        </div>
    </div>
    @endsection
    @section('script')
    <script>
        $(document).ready(function() {
                let questions = [];
                let currentQuestionIndex = 0;

                async function getQuestion() {
                    showLoading();
                    let id_tryout = '{{ $tryout->id }}';
                    const response = await $.ajax({
                        type: "POST",
                        url: "{{ route('tryout.getQuestions') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id_tryout: id_tryout
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                questions = response.data;
                            }
                        },
                    });

                }

                function displayQuestion(index) {
                    const question = questions[index];
                    $("#question-content").html(`
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold">Nomor <span id="nomor_soal">${question.nomor}</span></h6>
                        <div id="status_soal-${index}" class="alert ${
                            question.user_answer?.status === 'dijawab'
                                ? 'alert-info'
                                : question.user_answer?.status === 'belum_dijawab'
                                ? 'alert-light'
                                : question.user_answer?.status === 'ragu-ragu'
                                ? 'alert-warning'
                                : 'alert-danger'
                        } py-1 px-2 my-2" role="alert">
                            ${question.user_answer?.status === 'dijawab'
                                ? 'Sudah dijawab'
                                : question.user_answer?.status === 'ragu-ragu'
                                ? 'Ragu-ragu'
                                : 'Belum dijawab'}
                        </div>


                    </div>
                    <div class="card-body">
                        <small style="font-size: 14px;" id="soal_tryout">${question.soal}</small>
                        <div class="form-check">
                            <input class="form-check-input setAnswer" data-id="${question.id}" data-pilihan="a" data-index="${index}" ${question.user_answer?.jawaban != null && question.user_answer?.jawaban == 'a' ? 'checked' : ''} type="radio" name="answer-${index}-" id="answer-${index}-A" value="a">
                            <label class="form-check-label" data-pilihan="a" data-id="${question.id}" data-index="${index}"  style="font-size: 14px;" for="answer-${index}-A">
                                A. ${question.pilihan_a}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input setAnswer" data-id="${question.id}" data-pilihan="b" data-index="${index}" ${question.user_answer?.jawaban != null && question.user_answer?.jawaban == 'b' ? 'checked' : ''} type="radio" name="answer-${index}-" id="answer-${index}-B" value="b">
                            <label class="form-check-label" data-pilihan="b" data-id="${question.id}" data-index="${index}"  style="font-size: 14px;" for="answer-${index}-B">
                                B. ${question.pilihan_b}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input setAnswer" data-id="${question.id}" data-pilihan="c" data-index="${index}" ${question.user_answer?.jawaban != null && question.user_answer?.jawaban == 'c' ? 'checked' : ''} type="radio" name="answer-${index}-" id="answer-${index}-C" value="c">
                            <label class="form-check-label" data-pilihan="c" data-id="${question.id}" data-index="${index}"  style="font-size: 14px;" for="answer-${index}-C">
                                C. ${question.pilihan_c}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input setAnswer" data-id="${question.id}" data-pilihan="d" data-index="${index}" ${question.user_answer?.jawaban != null && question.user_answer?.jawaban == 'd' ? 'checked' : ''} type="radio" name="answer-${index}-" id="answer-${index}-D" value="d">
                            <label class="form-check-label" data-pilihan="d" data-id="${question.id}" data-index="${index}"  style="font-size: 14px;" for="answer-${index}-D">
                                D. ${question.pilihan_d}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input setAnswer" data-id="${question.id}" data-pilihan="e" data-index="${index}" ${question.user_answer?.jawaban != null && question.user_answer?.jawaban == 'e' ? 'checked' : ''} type="radio" name="answer-${index}-" id="answer-${index}-E" value="e">
                            <label class="form-check-label" data-pilihan="e" data-id="${question.id}" data-index="${index}"  style="font-size: 14px;" for="answer-${index}-E">
                                E. ${question.pilihan_e}
                            </label>
                        </div>
                    </div>
                    <div class="card-footer border-0 bg-white px-4">
                    <div class="row justify-content-between mb-3">
                        <a href="" type="button" class="nav-link col-5 text-primary ms-3">Nilai Normal Lab</a>
                        <div class="form-check col-6 d-flex justify-content-end gap-2">
                            <input style="cursor: pointer; border: solid 1px #595959FF" class="form-check-input ragu-ragu-input"
                                type="checkbox" data-id="${question.id}" data-index="${index}" ${question.user_answer?.status === 'ragu-ragu' ? 'checked' : ''} id="ragu" name="ragu">
                            <label data-id="${question.id}" data-index="${index}" style="cursor: pointer;" class="form-check-label" for="ragu">
                                Ragu-ragu
                            </label>
                        </div>
                    </div>
                    <div class="row justify-content-between mb-3">
                        <div class="col-6">
                            <button id="prev-btn" class="btn btn-primary" ${index === 0 ? 'disabled' : ''}>Sebelumnya</button>
                        </div>
                        <div id="next-btn" class="col-6 d-flex justify-content-end">
                        ${index == questions.length - 1 ? '<button class="btn btn-primary finished">Selesai</button>' : '<button class="btn btn-primary">Selanjutnya</button>'}
                        </div>
                    </div>
                </div>
                `);

                    // Update tombol navigasi
                    $("#prev-btn").prop("disabled", index === 0);
                    $("#next-btn").prop("disabled", index === questions.length - 1);

                    $(".question-number").removeClass("active");
                    $(`#question-number-${index}`).addClass("active");


                }


                $(document).on('click', '.setAnswer', function() {
                    const index = $(this).data('index');
                    const pilihan = $(this).data('pilihan');
                    const id = $(this).data('id');
                    const previousStatus = questions[index].user_answer?.status || 'belum_dijawab';
                    const status = $('input.ragu-ragu-input:checked').length > 0 ? 'ragu-ragu' : 'dijawab';
                    questions[index].user_answer = {
                        jawaban: pilihan,
                        status: status
                    };

                    if (status === 'dijawab' && previousStatus !== 'dijawab') {
                        $('#jumlah_soal_dijawab').text(parseInt($('#jumlah_soal_dijawab').text()) + 1);
                    }


                    if (status === 'dijawab') {
                        $('#status_soal-' + index)
                            .removeClass('alert-light alert-warning alert-danger')
                            .addClass('alert-info')
                            .html('Sudah dijawab');
                        $(`#question-number-${index}`)
                            .removeClass('belum_dijawab ragu-ragu')
                            .addClass('dijawab');
                    } else {
                        $('#status_soal-' + index)
                            .removeClass('alert-light alert-info alert-danger')
                            .addClass('alert-warning')
                            .html('Ragu-ragu');
                        $(`#question-number-${index}`)
                            .removeClass('dijawab belum_dijawab')
                            .addClass('ragu-ragu');
                    }


                    $.ajax({
                        url: "{{ route('tryout.saveAnswer') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            pilihan: pilihan,
                            status: status,
                        },
                        success: function(response) {
                            console.log(response);
                        },
                        error: function(xhr, status, error) {
                            console.log(error);
                        }
                    });
                })

                $(document).on('change', '.ragu-ragu-input', function() {
                    const index = $(this).data('index');
                    const id = $(this).data('id');
                    const isChecked = $(this).is(':checked');
                    const selectedAnswer = $(`input[name="answer-${index}-"]:checked`).val();

                    let status = '';
                    if (isChecked) {
                        status = 'ragu-ragu';
                    } else {
                        status = selectedAnswer ? 'dijawab' : 'belum_dijawab';
                    }

                    // Simpan status lama untuk dibandingkan
                    const previousStatus = questions[index].user_answer?.status;

                    if (questions[index].user_answer === null) {
                        questions[index].user_answer = {
                            status: status
                        };
                    } else {
                        questions[index].user_answer.status = status;
                    }

                    // Perbarui jumlah soal dijawab hanya jika ada perubahan status yang relevan
                    if (status === 'ragu-ragu' && previousStatus === 'dijawab') {
                        $('#jumlah_soal_dijawab').text(parseInt($('#jumlah_soal_dijawab').text()) - 1);
                    } else if (status === 'dijawab' && previousStatus !== 'dijawab') {
                        $('#jumlah_soal_dijawab').text(parseInt($('#jumlah_soal_dijawab').text()) + 1);
                    }

                    // Perbarui tampilan status
                    if (status === 'ragu-ragu') {
                        $('#status_soal-' + index)
                            .removeClass('alert-light alert-info alert-danger')
                            .addClass('alert-warning')
                            .html('Ragu-ragu');
                        $(`#question-number-${index}`)
                            .removeClass('dijawab belum_dijawab')
                            .addClass('ragu-ragu');
                    } else if (status === 'dijawab') {
                        $('#status_soal-' + index)
                            .removeClass('alert-light alert-warning alert-danger')
                            .addClass('alert-info')
                            .html('Sudah dijawab');
                        $(`#question-number-${index}`)
                            .removeClass('belum_dijawab ragu-ragu')
                            .addClass('dijawab');
                    } else if (status === 'belum_dijawab') {
                        $('#status_soal-' + index)
                            .removeClass('alert-light alert-info alert-warning')
                            .addClass('alert-danger')
                            .html('Belum dijawab');
                        $(`#question-number-${index}`)
                            .removeClass('dijawab ragu-ragu')
                            .addClass('belum_dijawab');
                    }

                    // Kirim perubahan ke server
                    $.ajax({
                        url: "{{ route('tryout.saveAnswer') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            pilihan: selectedAnswer,
                            status: status,
                        },
                        success: function(response) {
                            console.log(response);
                        },
                        error: function(xhr, status, error) {
                            console.log(error);
                        }
                    });
                });


                function populateQuestionGrid() {
                    $("#question-grid").html(
                        questions.map((question, index) => `
                        <div id="question-number-${index}" style="cursor: pointer;" class="border question-number ${question.user_answer?.status === 'dijawab' ? 'dijawab' : (question.user_answer?.status === 'belum_dijawab' ? 'belum_dijawab' : (question.user_answer?.status === 'ragu-ragu' ? 'ragu-ragu' : 'belum_dijawab'))}  rounded p-2 text-center">
                            ${question.nomor}
                        </div>
                    `).join("")
                    );
                }

                $(document).on('click', "#prev-btn", function() {
                    if (currentQuestionIndex > 0) {
                        currentQuestionIndex--;
                        displayQuestion(currentQuestionIndex);
                    }
                });

                $(document).on('click', "#next-btn", function() {
                    if (currentQuestionIndex < questions.length - 1) {
                        currentQuestionIndex++;
                        displayQuestion(currentQuestionIndex);
                    }
                });



                $(document).on("click", "#grid_soal_belum_dijawab", () => filterQuestions(".belum_dijawab"));
                $(document).on("click", "#grid_soal_dijawab", () => filterQuestions(".dijawab"));
                $(document).on("click", "#grid_soal_ragu-ragu", () => filterQuestions(".ragu-ragu"));
                $(document).on("click", "#grid_soal_semua", () => filterQuestions(""));

                function filterQuestions(status) {
                    // Hapus highlight pada semua tombol
                    $('#question-grid-header button').removeClass('btn-light');

                    // Highlight tombol yang aktif
                    if (status) {
                        $(`#grid_soal_${status.replace(".", "")}`).addClass('btn-light');
                    } else {
                        $('#grid_soal_semua').addClass('btn-light');
                    }

                    // Sembunyikan semua nomor soal
                    $(".question-number").addClass("d-none");

                    // Tampilkan soal sesuai filter
                    if (status) {
                        $(`.question-number${status}`).removeClass("d-none");
                    } else {
                        $(".question-number").removeClass("d-none"); // Tampilkan semua soal
                    }
                }


                $(document).on('click', ".question-number", function() {
                    const index = $(this).text() - 1;
                    currentQuestionIndex = index;
                    displayQuestion(currentQuestionIndex);
                });


                getQuestion().then(() => {
                    $('#jumlah_soal').text(questions.length);
                    $('#jumlah_soal_dijawab').text(questions.filter(question => question.user_answer?.status ===
                        'dijawab').length);
                    populateQuestionGrid();
                    displayQuestion(currentQuestionIndex);
                });



            });



            $(document).ready(function () {

            let timeLeft = {{ $tryout->waktu }} * 60; // Ubah waktu awal dari menit ke detik
            const idTryout = "{{ $tryout->id }}";

            // Fungsi untuk memperbarui tampilan waktu
            function updateCountdownDisplay(time) {
                const hours = Math.floor(time / 3600); // Hitung jam
                const minutes = Math.floor((time % 3600) / 60); // Hitung menit
                const seconds = time % 60; // Hitung detik

                $('#hours').text(String(hours).padStart(2, '0'));
                $('#minutes').text(String(minutes).padStart(2, '0'));
                $('#seconds').text(String(seconds).padStart(2, '0'));
            }

                    const interval = setInterval(() => {
                    if (timeLeft <= 0) {
                        clearInterval(interval);
                        alert("Waktu habis!");
                        return;
                    }

                    timeLeft = timeLeft - 1; // Kurangi 1 detik
                    updateCountdownDisplay(timeLeft);

                    // Perbarui server setiap menit
                    if (timeLeft % 60 === 0) { // Jika waktu sisa kelipatan 60 detik
                        updateTimeOnServer();
                    }
                }, 1000); // Perbarui setiap 1 detik

            // Fungsi untuk mendapatkan waktu awal dari server
            function fetchInitialTime() {
                $.ajax({
                    url: `{{ route('tryout.getTimeLeft') }}`,
                    method: 'GET',
                    data: { id_tryout: idTryout },
                    success: function(response) {
                        if (response.status === 'success') {
                            timeLeft = response.sisa_waktu * 60; // Ubah dari menit ke detik
                            updateCountdownDisplay(timeLeft); // Perbarui tampilan awal

                        } else {
                            console.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching time:', error);
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            }

            // Fungsi untuk memperbarui waktu di server
            function updateTimeOnServer() {
                $.ajax({
                    url: "{{ route('tryout.saveTimeLeft') }}",
                    method: 'POST',
                    data: {
                        id_tryout: idTryout,
                        sisa_waktu: Math.ceil(timeLeft / 60), // Kirim waktu sisa dalam menit
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if (response.status !== 'success') {
                            console.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating time:', error);
                    }
                });
            }


            fetchInitialTime();
        });

    </script>
    @endsection