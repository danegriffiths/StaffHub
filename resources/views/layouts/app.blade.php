<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>{{ $title }}</title>
      <style>
          .btn-group button, .btn-group a {
              border: 1px solid white; /* Green border */
          }
      </style>
  </head>
  <body>
    @include('partials.navbar')
    <br>
    <div class="container">
        <div class="jumbotron text-center text-white" style="background-color: #b5b5b5 !important; padding-top: 10px; padding-bottom: 10px" >
          <h1 class="display-4">{{ $title }}</h1>
        </div>
        @if (session('message'))
          <div class="container">
            <div class="alert alert-success fade-message" role="alert">
              {{ session('message') }}
            </div>
          </div>
        @endif
        <div class="container">
          @foreach ($errors->all() as $error)
            <div class="alert alert-danger fade-message" role="alert">
              {{ $error }}
            </div>
          @endforeach
        </div>
        <div class="container">
                @yield('content')
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(function(){
            setTimeout(function() {
                $('.fade-message').slideUp();
            }, 1500);
        });
    </script>
  </body>
</html>
