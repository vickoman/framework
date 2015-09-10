:extends Main

:block title
   404 Not Found
:endblock

:block main
   <style type="text/css">
      .bitphp-error-main {
         background-color: #222334;
      }
   </style>
   <div class="header large-header alt vert">
      <div class="container">
         <h1>404 Page not found</h1>
         <p class="lead">The content that you search don't exists, try with <a class="red" href="{{ :base }}">main page.</a></p>
      </div>      
   </div>
:endblock