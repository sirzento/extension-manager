<style>
    #drop-zone {
        border: 2px dashed #ccc;
        border-radius: 10px;
        text-align: center;
        line-height: 75px;
        font-family: Arial, sans-serif;
        color: #aaa;
    }

    #drop-zone.dragover {
        background-color:rgb(84, 190, 12);
        border-color: #333;
        color: #333;
    }
</style>
<button class="btn btn-primary" data-toggle="modal" data-target="#settingsModal" style="margin-bottom: 10px;">Settings</button>
<div class="row" style="padding-left: 15px; padding-right: 10px;">
  @foreach ($blueprint->extensions() as $extension)
  @php
    $icon = !empty($extension['icon']) 
            ? '/assets/extensions/'.$extension['identifier'].'/icon.'.pathinfo($extension['icon'], PATHINFO_EXTENSION)
            : '/assets/extensions/'.$extension['identifier'].'/icon.jpg';
  @endphp
  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 text-center" style="padding-left: 0px; padding-right: 17px;">
    <div class="extension-btn" style="width:100%;margin-bottom:17px;">
      <div class="extension-btn-overlay"></div>
      <img src="{{ $icon }}" alt="{{ $extension['identifier']}}" class="extension-btn-image2"/>
      <img src="{{ $icon }}" alt="" class="extension-btn-image"/>
      <p class="extension-btn-text">{{ $extension['name'] }}</p>
      <p class="extension-btn-version">{{ $extension['version'] }}</p>
      <i class="bi bi-trash-fill text-danger" style="font-size: 25px;position: absolute;top: 23px;right: 30px;cursor: pointer;" data-toggle="modal" data-target="#deleteModal" data-identifier="{{ $extension['identifier'] }}"></i>
    </div>
  </div>
  @endforeach
  <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 text-center" style="padding-left: 0px; padding-right: 17px;">
    <div class="extension-btn" style="width:100%;margin-bottom:17px;">
      <form id="upload-form" action="/extensions/{identifier}/upload" method="POST" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div id="drop-zone">Drag and drop to install</div>
          <input type="file" name="file" id="file-input" style="visibility: hidden;" hidden>
          <input type="password" name="password" id="installPassword" value="" style="visibility: hidden;" hidden>
      </form>
  </div>
  </div>
</div>



<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="background-color:transparent">
      <form action="" method="POST" autocomplete="off">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;box-shadow:none"><span aria-hidden="true"><i class="bi bi-x"></i></span></button>
          <h3 class="modal-title">
            <img src="/assets/extensions/blueprint/logo.jpg" alt="logo" height="34" width="34" class="pull-left" style="border-radius:3px;margin-right:10px"/>
            Settings
          </h3>
        </div>

        <div class="modal-body">
          SSH Username
        <input 
          type="text"
          name="user"
          id="user"
          value="{{ $user }}"
          placeholder="root"
          class="form-control"
        />
        </div>

        <div class="modal-footer">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="PATCH">

          <div class="row">
            <div class="col-sm-10">
            </div>
            <div class="col-sm-2">
              <button type="submit" class="btn btn-success btn-sm" style="width:100%; margin-top:10px; margin-bottom:10px; border-radius:6px">Save</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="background-color:transparent">
      <form action="/extensions/{identifier}/deleteExtension" method="POST" autocomplete="off">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;box-shadow:none"><span aria-hidden="true"><i class="bi bi-x"></i></span></button>
          <h3 class="modal-title">
            <img src="/assets/extensions/blueprint/logo.jpg" alt="logo" height="34" width="34" class="pull-left" style="border-radius:3px;margin-right:10px"/>
            Delete extension <strong class="font-weight-bold modal-identifier"></strong>
          </h3>
        </div>

        <div class="modal-body">
          Should the extension <strong class="font-weight-bold modal-identifier"></strong> really be deleted?
          <br><br>
          Password confirmation for SSH user <strong>{{ $user }}</strong>
          <input type="password" name="password" value="" class="form-control">
        </div>

        <div class="modal-footer">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="POST">
          <input type="hidden" name="identifier" value="" id="identifier">
          <div class="row">
            <div class="col-sm-10">
              <p class="text-muted small text-left">Deleting extensions wont clear all of the configured extension settings. Reinstalling them later will fully restore the settings.</p>
            </div>
            <div class="col-sm-2">
              <button type="submit" class="btn btn-danger btn-sm" style="width:100%; margin-top:10px; margin-bottom:10px; border-radius:6px">Delete</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Install Modal -->
<div class="modal fade" id="installModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="background-color:transparent">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;box-shadow:none"><span aria-hidden="true"><i class="bi bi-x"></i></span></button>
        <h3 class="modal-title">
          <img src="/assets/extensions/blueprint/logo.jpg" alt="logo" height="34" width="34" class="pull-left" style="border-radius:3px;margin-right:10px"/>
          Install extension
        </h3>
      </div>

      <div class="modal-body">
        Password confirmation for SSH user <strong>{{ $user }}</strong>
        <input type="password" name="password" id="installPasswordModal" value="" class="form-control">
      </div>

      <div class="modal-footer">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="POST">
        <div class="row">
          <div class="col-sm-10">
          </div>
          <div class="col-sm-2">
            <button type="button" onclick="uploadAndInstall()" class="btn btn-primary btn-sm" style="width:100%; margin-top:10px; margin-bottom:10px; border-radius:6px">Install</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  (async() => {
    while(!window.hasOwnProperty("jQuery")) {
        await new Promise(resolve => setTimeout(resolve, 100));
    }
    $('#deleteModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget)
      var identifier = button.data('identifier')
  
      var modal = $(this)
      modal.find('.modal-identifier').text(identifier)
      modal.find('#identifier').val(identifier)
    })

    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const form = document.getElementById('upload-form');

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');

        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            $('#installModal').modal()
        }
    });
  })();
  function uploadAndInstall() {
    document.getElementById('installPassword').value = document.getElementById('installPasswordModal').value;
    document.getElementById('upload-form').submit();
  }
</script>