<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <meta name="description" content="Certificado">

    <link rel="stylesheet" href="/assets/css/my-styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  </head>

  <body>
    <div id="mainContent">
      <div id="contentCertificate" class="ticket-email">
        <div class="first-content">
            <div id="certificate" class="banner"></div>

            <div class="banner-controls">                
                <button onclick="addBackgroundCertificate();" id="btnEdit" type="button" class="btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"></path>
                    </svg>
                </button>
                <button onclick="removeBackgroundCertificate();" id="btnRemove" type="button" class="btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                        <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"></path>
                    </svg>
                </button>
                <input onchange="previewFile(event);" id="inputFile" type="file" name="fileBackground" class="input-file" />
            </div>
        </div>

        <div class="content-buttons">     
            <div class="content-name">
              <label for="" class="form-label">Nombre del certificado</label>
              <input id="field-name" oninput="validateInput(this.value)" type="text" class="form-name" required>
              <div id="error-name" class="invalid-feedback" style="display:none;"></div>
            </div>  
            
            <hr class="line">
            
            <div id="tools">
                <button onclick="addTextField();" class="btn">Adicionar Texto</button>
                <button onclick="addImage();" class="btn">Adicionar Imagen</button>
            </div>

            <hr class="line">

            <div id="actions">
                <button onclick="actionCancel();" class="btn btn-secondary">cancelar</button>
                <button onclick="actionSave();" class="btn">Guardar</button>
            </div>
        </div>
      </div>
    </div>

    <script>
      var event_id = @json($event);
      var registrationFields = @json($eventRegistrationFields);
      var baseUrl =  @json(env("APP_URL"));
    </script>

    <script src="/assets/js/my-script.js"></script>
  </body>
</html>