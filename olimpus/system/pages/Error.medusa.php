:extends Main

:block title
   Opps! | Algo salio mal
:endblock

:block main
   :var nerrors count($errors)

   <div class="featurette">
      <div class="container-fluid">
         <h1>Ocurrieron {{ $nerrors }} errores!</h1>
         /* for check if error log have permissions */
         :if !$errors[0]['identifier']
            <h4>No se pudieron registrar los errores, verifica qué el servidor tenga permisos de escritura en la carpeta <b>{{ $_ROUTE['base_path'] }}/olimpus</b> o revisa la configuración.</h4>
         :endif
         <br>
         :foreach $errors as $error
            <h4 class="force-non-upper">
               <span class="red"># </span>
               {{ $error.message }} - <i>{{ $error.file }} linea {{ $error.line }}</i>
            </h4>
            :if $error.identifier
               <div class="alert alert-bitphp">
                  <span class="glyphicon glyphicon-console pull-right"></span>
                  $ php dummy error --id {{ $error.identifier }}
               </div>
            :endif
         :endforeach
         <br>
      </div>
   </div>
   <div class="gallery">
      <div class="row">
         <div class="col-md-6 col-md-offset-3 text-center">
            <h2 class="white">Variables de entorno</h2>
            <br>
         </div>
      </div>
      <div class="container well well-lg">
         <div class="row">
            <div class="col-sm-4">
               <div class="list-group">
                  <a href="#" class="list-group-item">
                     <span class="glyphicon glyphicon-leaf"></span>
                     <b>Environment</b> <br> {{ \Bitphp\Core\Environment::info()['current'] }}
                  </a>
                  <br>
                  <a href="#" class="list-group-item">
                     <span class="glyphicon glyphicon-folder-open"></span>
                     <b>Base path</b> <br> {{ $_ROUTE['base_path'] }}
                  </a>
                  <br>
                  <a href="#" class="list-group-item">
                     <span class="glyphicon glyphicon-globe"></span>
                     <b>Base URL</b> <br> {{ $_ROUTE['base_url'] }}
                  </a>
                  <br>
                  <a href="#" class="list-group-item">
                     <span class="glyphicon glyphicon-question-sign"></span>
                     <b>GET</b>
                     <br> 
                     <pre>{{ filter_var(var_export($_GET, true), FILTER_SANITIZE_FULL_SPECIAL_CHARS) }}</pre>
                  </a>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="list-group">
                  <a href="#" class="list-group-item">
                     <span class="glyphicon glyphicon-link"></span>
                     <b>Request URI</b> <br> /{{ $_ROUTE['request_uri'] }}
                  </a>
                  <br>
                  <a href="#" class="list-group-item">
                     <span class="glyphicon glyphicon-floppy-disk"></span>
                     <b>App path</b> <br> {{ $_ROUTE['app_path'] }}
                  </a>
                  <br>
                  <a href="#" class="list-group-item">
                     <span class="glyphicon glyphicon-grain"></span>
                     <b>COOKIES</b>
                     <br> 
                     <pre>{{ filter_var(var_export($_COOKIE, true), FILTER_SANITIZE_FULL_SPECIAL_CHARS) }}</pre>
                  </a>
               </div>
            </div>
            <div class="col-sm-4">
               <div class="list-group">
                  <a href="#" class="list-group-item">
                     <span class="glyphicon glyphicon-paperclip"></span>
                     <b>URI Params</b> 
                     <br>
                     :foreach $_ROUTE['uri_params'] as $param
                        {{ $param }}<br>
                     :endforeach
                  </a>
                  <br>
                  <a href="#" class="list-group-item">
                     <span class="glyphicon glyphicon-open-file"></span>
                     <b>POST</b>
                     <br> 
                     <pre>{{ filter_var(var_export($_POST, true), FILTER_SANITIZE_FULL_SPECIAL_CHARS) }}</pre>
                  </a>
                  <br>
                  <a href="#" class="list-group-item">
                     <span class="glyphicon glyphicon-inbox"></span>
                     <b>Standard PHP Input</b>
                     <br> 
                     <pre>{{ filter_var(var_export(file_get_contents('php://input'), true), FILTER_SANITIZE_FULL_SPECIAL_CHARS) }}</pre>
                  </a>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="blurb bright text-center">
      <h2>Información extra</h2>
   </div>
   <div class="blurb">
      <div class="container">
         <div class="row">
            <h3>Traza inversa</h3>
         </div>
         <div class="row">
            <div class="table-responsive">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th>Hop</th>
                        <th>Archivo</th>
                        <th>Linea</th>
                        <th>Clase y/o objeto</th>
                        <th>Argumentos</th>
                     </tr>
                  </thead>
                  <tbody>
                     :var hopCounter 0
                     :foreach $errors[0]['trace'] as $hop
                        <tr>
                           <th>{{ $hopCounter }}</th>
                           <td>
                              :if !empty($hop.file)
                                 :if preg_match('/(.*)\/bitphp\/(.*)/', $hop.file)
                                    {{ $hop.file }}
                                 :else
                                    <strong>{{ $hop.file }}</strong>
                                 :endif
                              :endif
                           </td>
                           <td>
                              :if !empty($hop.line)
                                 {{ $hop.line }}
                              :endif
                           </td>
                           <td>
                              :if !empty($hop.class)
                                 {{ $hop.class }}
                              :endif
                              :if !empty($hop.type)
                                 {{ $hop.type }}
                              :endif
                              :if !empty($hop.function)
                                 {{ $hop.function }}
                              :endif
                           </td>
                           <td>
                              :if !empty($hop.args)
                                 {{ filter_var(var_export($hop.args, true), FILTER_SANITIZE_FULL_SPECIAL_CHARS) }}
                              :endif
                           </td>
                        </tr>
                        <?php $hopCounter++ ?>
                     :endforeach
                  </tbody>
               </table>
            </div>
         </div>
         <div class="row">
            <h3>Cabeceras HTTP</h3>
         </div>
         <div class="row">
            <div class="table-responsive">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th>Nombre</th>
                        <th>Valor</th>
                     </tr>
                  </thead>
                  <tbody>
                     :foreach getallheaders() as $name => $value
                        <tr>
                           <th>{{ filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS) }}</th>
                           <td>{{ filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS) }}</td>
                        </tr>
                     :endforeach
                  </tbody>
               </table>
            </div>
         </div>
         <div class="row">
            <h3>Configuración cargada</h3>
         </div>
         <div class="row">
            <div class="table-responsive">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th>Parametro</th>
                        <th>Valor</th>
                     </tr>
                  </thead>
                  <tbody>
                     :foreach \Bitphp\Core\Config::all() as $param => $value
                        <tr>
                           <th>{{ $param }}</th>
                           <td>
                              <pre><?php var_dump($value) ?></pre>
                           </td>
                        </tr>
                     :endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   <br><br>
:endblock