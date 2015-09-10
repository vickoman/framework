:extends Main

:block title
   ({{ $delay }}) Redirection..
:endblock

:block main
   <div class="header large-header alt vert">
      <div class="container">
         <h1>Oops! please wait...</h1>
         <p class="lead">You will be redirected in <b id="counter"></b> seconds</p>
      </div>      
   </div>
   <script type="text/javascript">

   var url = "{{ $url }}";
   var limit   = {{ $delay }};
   var counter = document.getElementById("counter");

   counter.innerHTML = limit;

   setInterval(function(){
      limit--;

      if(limit <= 0) {
         window.location = url;
      }

      counter.innerHTML = limit;
      document.title = '(' + limit + ') Redirection...';
   }, 1000)

   </script>
:endblock