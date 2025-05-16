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
    .fa-spinner.spinning {
        animation: spin 1s infinite linear;
        -webkit-animation: spin2 1s infinite linear;
    }

    @keyframes spin {
        from { transform: scale(1) rotate(0deg); }
        to { transform: scale(1) rotate(360deg); }
    }

    @-webkit-keyframes spin2 {
        from { -webkit-transform: rotate(0deg); }
        to { -webkit-transform: rotate(360deg); }
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
          <input type="file" name="sshkey" id="installSshkey" value="" style="visibility: hidden;" hidden>
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

        SSH Port
        <input 
          type="text"
          name="port"
          id="port"
          value="{{ $port }}"
          placeholder="20"
          class="form-control"
        />
        <br>
        
        <input 
          type="checkbox"
          name="useSSHKey"
          id="useSSHKey"
          {{  ($useSSHKey == 1 ? ' checked' : '') }}
        />
        <label for="useSSHKey">Use SSH key instead of password for SSH auth.</label>
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
      <form action="/extensions/{identifier}/deleteExtension" method="POST" id="delete-form" enctype="multipart/form-data">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;box-shadow:none"><span aria-hidden="true"><i class="bi bi-x"></i></span></button>
          <h3 class="modal-title">
            <img src="/assets/extensions/blueprint/logo.jpg" alt="logo" height="34" width="34" class="pull-left" style="border-radius:3px;margin-right:10px"/>
            Delete extension <strong class="font-weight-bold modal-identifier"></strong>
          </h3>
        </div>

        <div class="modal-body">
          <span id="delete-body">
            Are you sure you want to delete the extension <strong class="font-weight-bold modal-identifier"></strong>?
            <br><br>
            @if($useSSHKey)
              SSH key for authentication (User <strong>{{ $user }}</strong>)
              <input type="file" name="sshkey" id="sshkey" class="form-control">
            @else
              Password confirmation for SSH user <strong>{{ $user }}</strong>
              <input type="password" name="password" value="" class="form-control">
            @endif
          </span>
          <span id="delete-loading" style="display: none;">
            <i class="fa fa-spinner spinning" aria-hidden="true" style="font-size: 25px;"></i>  Deleting extension...
          </span>
        </div>

        <div class="modal-footer">
          {{ csrf_field() }}
          <input type="hidden" name="identifier" value="" id="identifier">
          <div class="row">
            <div class="col-sm-10">
              <p class="text-muted small text-left">Deleting an extension does not remove its configuration data. If reinstalled later, all previous settings will be fully restored.</p>
            </div>
            <div class="col-sm-2">
              <button type="submit" class="btn btn-danger btn-sm" id="delete-button" style="width:100%; margin-top:10px; margin-bottom:10px; border-radius:6px">Delete</button>
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
        <span id="install-body">
          @if($useSSHKey)
            SSH key for authentication (User <strong>{{ $user }}</strong>)
            <input type="file" name="sshkey" id="installSshkeyModal" class="form-control">
          @else
            Password confirmation for SSH user <strong>{{ $user }}</strong>
            <input type="password" name="password" id="installPasswordModal" value="" class="form-control">
          @endif
        </span>
        <span id="install-loading" style="display: none;">
          <i class="fa fa-spinner spinning" aria-hidden="true" style="font-size: 25px;"></i>  Installing extension...
        </span>
      </div>

      <div class="modal-footer">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="POST">
        <div class="row">
          <div class="col-sm-10">
          </div>
          <div class="col-sm-2">
            <button type="button" onclick="uploadAndInstall()" id="install-button" class="btn btn-primary btn-sm" style="width:100%; margin-top:10px; margin-bottom:10px; border-radius:6px">Install</button>
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

    $(document).on('submit','#delete-form',function(){
      $('#delete-body').hide();
      $('#delete-loading').show();
      $('#delete-button').prop("disabled",true);
    });


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
    if (document.getElementById('installPasswordModal'))
      document.getElementById('installPassword').value = document.getElementById('installPasswordModal').value;
    if (document.getElementById('installSshkeyModal'))
      document.getElementById('installSshkey').files = document.getElementById('installSshkeyModal').files;
    document.getElementById('upload-form').submit();
    $('#install-body').hide();
    $('#install-loading').show();
    $('#install-button').prop("disabled",true);
  }
</script>