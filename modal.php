<!DOCTYPE html>
<html lang="en">
  <head>    
    <!-- Bootstrap core CSS -->
    <link href="bootstrap-3.3.6/docs/dist/css/bootstrap.min.css" rel="stylesheet">     
    <style>
    body {
        min-height: 2000px;
        padding-top: 70px;
    }

    .my-fluid-container {
        padding-left: 15px;
        padding-right: 15px;
        margin-left: auto;
        margin-right: auto;
    }

    .modal-backdrop {
        position: relative;
        z-index: -1;
    }
      </style>  
  </head>
<body>
    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="my-fluid-container">
        <!-- Filter Modal -->
        <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal title</h4>
              </div>
              <div class="modal-body">
                ...
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><strong>Site Informer</strong></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="#about">All Sites for Today</a></li>
            <li><a data-toggle="modal" href="#filterModal" class="btn">Launch demo modal</a></li>
          </ul>          
          <ul class="nav navbar-nav navbar-right">
            <li>
              <form class="navbar-form form-inline">
                <div class="btn-group">
                  <button class="btn btn-primary data-toggle="tooltip" data-placement="left" title="Edit Filter""><span class="glyphicon glyphicon-filter"></span></button>
                  <button class="btn btn-primary data-toggle="tooltip" data-placement="left" title="Refresh""><span class="glyphicon glyphicon-refresh"></span></button>
                  <button class="btn btn-primary data-toggle="tooltip" data-placement="left" title="Export to Excel""><span class="glyphicon glyphicon-export"></span></button>
                </div>
              </form>
            </li>
            <li><a href="#about">About</a></li>
          </ul>        
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div class="my-fluid-container">

      <!-- Main component for a primary marketing message or call to action -->
      <div class="row">
        <div class="col-md-12">
          Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
        </div>
      </div>

    </div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap-3.3.6/docs/assets/js/vendor/jquery.min.js"></script>
    <script src="bootstrap-3.3.6/docs/dist/js/bootstrap.min.js"></script>
        
  </body>
</html>
