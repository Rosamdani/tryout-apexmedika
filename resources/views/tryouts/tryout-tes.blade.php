@extends('layouts.index')

@section('title', 'Try Out')
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
                    <p class="fw-bold mb-0">1/150</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex gap-3 rounded mb-3 p-2" style="background-color: #E6E6E6FF;">
                <button class="btn text-secondary btn-light">Semua soal</button>
                <button class="btn text-secondary ">Soal Sudah Dijawab</button>
                <button class="btn text-secondary ">Soal Masih Ragu-ragu</button>
                <button class="btn text-secondary ">Soal Belum Dijawab</button>
            </div>
            <div id="question-grid"
                style="display: grid; grid-template-columns: repeat(20, minmax(0, 1fr)); gap: 0.5rem;">

            </div>
        </div>
    </div>
    @endsection
    @section('script')
    <script>
        $(document).ready(function () {
        let questions = [];
        let currentQuestionIndex = 0;

        async function getQuestion() {
            let id_tryout = '{{ $tryout->id }}';
            const response = await $.ajax({
                type: "POST",
                url: "{{ route('tryout.getQuestions') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id_tryout: id_tryout
                }
            });
            if (response.status == 'success') {
                questions = response.data;
            }
        }

        function displayQuestion(index) {
            const question = questions[index];
            $("#question-content").html(`
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold">Nomor <span id="nomor_soal">${question.nomor}</span></h6>
                    <div id="status_soal-${index}" class="alert ${question.status == 'sudah_dijawab' ? 'alert-info' : (question.status == 'belum_dijawab' ? 'alert-light' : (question.status == 'ragu-ragu' ? 'alert-warning' : 'alert-danger')) }  py-1 px-2 my-2" id="status_soal" role="alert">
                        ${question.status == 'sudah_dijawab' ? 'Sudah dijawab' : 'Belum dijawab'}
                    </div>

                </div>
                <div class="card-body">
                    <small style="font-size: 14px;" id="soal_tryout">${question.soal}</small>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answer1" id="answer1A" value="A">
                        <label class="form-check-label setAnswer" data-pilihan="a" data-id="${question.id}" data-index="${index}"  style="font-size: 14px;" for="answer1A">
                            A. ${question.pilihan_a}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answer1" id="answer1B" value="B">
                        <label class="form-check-label setAnswer" data-pilihan="b" data-id="${question.id}" data-index="${index}"  style="font-size: 14px;" for="answer1B">
                            B. ${question.pilihan_b}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answer1" id="answer1C" value="C">
                        <label class="form-check-label setAnswer" data-pilihan="c" data-id="${question.id}" data-index="${index}"  style="font-size: 14px;" for="answer1C">
                            C. ${question.pilihan_c}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answer1" id="answer1D" value="D">
                        <label class="form-check-label setAnswer" data-pilihan="d" data-id="${question.id}" data-index="${index}"  style="font-size: 14px;" for="answer1D">
                            D. ${question.pilihan_d}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answer1" id="answer1E" value="E">
                        <label class="form-check-label setAnswer" data-pilihan="e" data-id="${question.id}" data-index="${index}"  style="font-size: 14px;" for="answer1E">
                            E. ${question.pilihan_e}
                        </label>
                    </div>
                </div>
                <div class="card-footer border-0 bg-white px-4">
                <div class="row justify-content-between mb-3">
                    <a href="" type="button" class="nav-link col-5 text-primary ms-3">Nilai Normal Lab</a>
                    <div class="form-check col-6 d-flex justify-content-end gap-2">
                        <input style="cursor: pointer; border: solid 1px #595959FF" class="form-check-input"
                            type="checkbox" id="ragu" name="ragu">
                        <label onclick="submitRagu(${question.id}, ${index})" style="cursor: pointer;" class="form-check-label" for="ragu">
                            Ragu-ragu
                        </label>
                    </div>
                </div>
                <div class="row justify-content-between mb-3">
                    <div class="col-6">
                        <button id="prev-btn" class="btn btn-outline-primary">Sebelumnya</button>
                    </div>
                    <div id="next-btn" class="col-6 d-flex justify-content-end">
                        <button class="btn btn-primary">Selanjutnya</button>
                    </div>
                </div>
            </div>
            `);

            // Update tombol navigasi
            $("#prev-btn").prop("disabled", index === 0);
            $("#next-btn").prop("disabled", index === questions.length - 1);

            $(".question-number").removeClass("bg-warning-subtle border-warning");
            $(`#question-number-${index}`).addClass("bg-warning-subtle border-warning");


        }

        $(document).on('click', '.setAnswer', function () {
            const index = $(this).data('index');
            const pilihan = $(this).data('pilihan');
            const id = $(this).data('id');

            $('#status_soal-'+index).removeClass('alert-info alert-light alert-warning alert-danger');
            $('#status_soal-'+index).addClass('alert-info');
            $('#status_soal-'+index).html('Sudah dijawab');

            $.ajax({
                url: "{{ route('tryout.saveAnswer') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    pilihan: pilihan,
                },
                success: function (response) {
                    console.log(response);
                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        })

        function populateQuestionGrid() {
            $("#question-grid").html(
                questions.map((question, index) => `
                    <div id="question-number-${index}" style="cursor: pointer;" class="border question-number  rounded p-2 text-center">
                        ${question.nomor}
                    </div>
                `).join("")
            );
        }

        $(document).on('click',"#prev-btn", function () {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                displayQuestion(currentQuestionIndex);
            }
        });

        $(document).on('click', "#next-btn", function () {
            if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                displayQuestion(currentQuestionIndex);
            }
        });

        $(document).on('click', ".question-number", function () {
            const index = $(this).text() - 1;
            currentQuestionIndex = index;
            displayQuestion(currentQuestionIndex);
        });

        $("#question-grid").on("click", ".question-number", function () {
            const index = $(this).text() - 1;
            currentQuestionIndex = index;
            displayQuestion(currentQuestionIndex);
        });

        getQuestion().then(() => {
            populateQuestionGrid();
            displayQuestion(currentQuestionIndex);
        });

    });

    </script>
    @endsection