
@extends('layouts.app')

@section('title') Raport sprzedaży @endsection

@section('content')
<div class="container">
    <h1 class="text-center mb-4 mt-2">Raport sprzedaży</h1>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <form method="get" action="{{ route('sales.index') }}">
                <div class="row justify-content-center">
                    <div class="col-lg-6 form-group">
                        <label for="start_date">Początek</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-lg-6 form-group">
                        <label for="end_date">Koniec</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-lg-4">
                        <button type="submit" class="btn btn-primary w-100 my-3">Szukaj</button>
                    </div>
                    <div class="col-lg-4">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#createModal" class="btn btn-success w-100 my-3">Dodaj nowy rekord</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <hr class="my-2">

    @if (count($sales) == 0)
        <h3 class="text-danger text-center">Brak danych! Wybierz inny przedział czasowy</h3>
    @else
    <div>
        <canvas id="salesChart"></canvas>
    </div>

    <table class="table mt-4 text-center">
        <thead>
            <tr>
                <th>Data</th>
                <th>Wartość</th>
                <th>Opcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td>{{ $sale->date->format('d.m.Y') }}</td>
                    <td>{{ $sale->net_value }}</td>
                    <td>
                        <button class="btn btn-secondary" onclick="editRecord('{{ $sale->date->format('Y-m-d') }}', {{ $sale->net_value }})"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button class="btn btn-danger" onclick="deleteRecord({{ $loop->index }})"><i class="fa-solid fa-xmark"></i></button>
                        <form action="{{ route('sales.destroy') }}" method="post" id="formsale{{ $loop->index }}">
                            @csrf
                            @method('delete')
                            <input type="date" name="date" value="{{ $sale->date->format('Y-m-d') }}" class="d-none" required>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="createModalLabel">Dodanie danych</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('sales.store') }}" method="post" id="createModalForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="start_date">Data</label>
                    <input type="date" id="date_create" name="date" class="form-control" value="{{ old('date') }}" required>
                </div>
                <div class="form-group">
                    <label for="net_value">Wartość</label>
                    <input type="number" step="any" id="net_value_create" name="net_value" class="form-control"  value="{{ old('net_value') }}" pattern="^\d*(\.\d{0,2})?$" min="0" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                <button type="submit" class="btn btn-primary">Dodaj rekord</button>
              </div>
        </form>
      </div>
    </div>
  </div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="editModalLabel">Edycja danych</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('sales.update') }}" method="post" id="editModalForm">
            @csrf
            @method('put')
            <div class="modal-body">
                <div class="form-group">
                    <label for="start_date">Data</label>
                    <input type="date" id="date_edit" name="date" class="form-control" value="" required disabled>
                </div>
                <div class="form-group">
                    <label for="net_value">Wartość</label>
                    <input type="number" step="any" id="net_value_edit" name="net_value" class="form-control" value="" min="0" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
              </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('script')
<script>
    let ctx = document.getElementById('salesChart').getContext('2d');
    let data = {
        labels: [
            @foreach ($sales as $sale)
                '{{ $sale->date->format('Y-m-d') }}',
            @endforeach
        ],
        datasets: [{
            label: 'Wartość',
            data: [
                @foreach ($sales as $sale)
                    {{ $sale->net_value }},
                @endforeach
            ],
            backgroundColor: 'rgba(97, 94, 252, 0.5)',
            borderColor: 'rgba(97, 94, 252, 1)',
            borderWidth: 1
        }]
    };

    let trendline = {
        type: 'line',
        label: 'Linia trendu',
        data: Trendline(data.datasets[0].data),
        fill: false,
        borderColor: 'rgba(210, 0, 98, 1)',
        borderWidth: 2
    };

    let chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                data.datasets[0],
                trendline
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function Trendline(data) {

        // Set base data
        let quantity = data.length;
        let x = 0, y = 0, xy = 0, xx = 0;

        // Calculate vars for formula
        for (var i = 0; i < quantity; i++) {
            x += i;
            y += data[i];
            xy += i * data[i];
            xx += i * i;
        }

        //Calculate Trend Line
        let slope = (quantity * xy - x * y) / (quantity * xx - x**2);
        let intercept = (y - slope * x) / quantity;

        //Make an array of Trend Line
        let result = [];
        for (let i = 0; i < quantity; i++) {
            // Math formula for Line (y = ax + b)
            result.push(slope * i + intercept);
        }
        return result;
    }
</script>

@if ($startDate > $endDate)
<script>
    Swal.fire({
    icon: "error",
    title: "Błąd!",
    text: "Data rozpoczęcia raportu jest większa niż data zakończenia.",
    confirmButtonColor: "#0d6efd",
    confirmButtonText: "Odśwież stronę",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route('sales.index') }}';
        }
    });
</script>
@endif

<script>
    function deleteRecord(id)
    {
        Swal.fire({
            title: "Czy chcesz usunąć ten rekord?",
            showCancelButton: true,
            confirmButtonText: "Usuń",
            }).then((result) => {
            if (result.isConfirmed) {
                $('#formsale'+id).submit();
            }
            });
    }

    function editRecord(date, value)
    {
        $('#date_edit').val(date);
        $('#net_value_edit').val(value);
        $('#editModal').modal('show');
    }
</script>

{{-- ALERTS --}}

@if (session('fail_created'))
<script>
    Swal.fire({
    icon: "danger",
    title: "Błąd!",
    text: "Taki rekord znajduje się w bazie",
    });
</script>
@endif

@if (session('created'))
<script>
    Swal.fire({
    icon: "success",
    title: "Sukces!",
    text: "Rekord został dodany pomyślnie",
    });
</script>
@endif

@if (session('edited'))
<script>
    Swal.fire({
    icon: "success",
    title: "Sukces!",
    text: "Rekord został zakutalizowany pomyślnie",
    });
</script>
@endif

@if (session('deleted'))
<script>
    Swal.fire({
    icon: "success",
    title: "Sukces!",
    text: "Rekord został usunięty pomyślnie",
    });
</script>
@endif

@if ($errors->any())
<script>
    Swal.fire({
        icon: "error",
        title: "Błąd!",
        text: "{{ $errors->first() }}",
        });
</script>
@endif

@endsection
