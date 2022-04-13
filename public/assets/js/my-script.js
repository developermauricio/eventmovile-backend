
/***  Crear array de opciones para los campos  ***/
let fields = [
    {
        id: -1,
        name: "Nombre",
        column: "name"
    },
    {
        id: -1,
        name: "Apellido",
        column: "lastname"
    },
    {
        id: -1,
        name: "Nombre Completo",
        column: "fullname"
    },
    {
        id: -1,
        name: "Email",
        column: "email"
    },
]

/***  Adicionar los campos dinamicos del formulario del evento a los campos por defecto  ***/
if ( registrationFields.length ) {
    registrationFields.forEach( item => {
        fields.push( item );
    });
}



/***  variables  ***/
let fileBackground = null;
let urlBackground = '';

const defaultImage = `${baseUrl}/assets/img/certificate-image-null.png`;
let imageContainer;

let activeItem = null;
let active = false;
let components = [];

/***  componentes  ***/
const contentCertificate = document.getElementById('certificate');


/***  Eventos clicks  ***/
const addBackgroundCertificate = () => {
    document.getElementById('inputFile').click()
}

const previewFile = ( event ) => {
    const [ File ] = event.target.files;
    fileBackground = File
    urlBackground = URL.createObjectURL( fileBackground )
    contentCertificate.style.backgroundImage = `url(${urlBackground})`;
}

const removeBackgroundCertificate = () => {
    fileBackground = null
    urlBackground = ''
    contentCertificate.style.backgroundImage = `url(${urlBackground})`;
    document.getElementById('inputFile').value = ''
}


const addTextField = () => {

    /* if(item != null && item['type'] == 'image'){
		addImage(item);
		return;
	} */

    const component = {
        type:'text',
        width:300, 
        x:0, 
        y:36, 
        size:24, 
        fontFamily:'Helvetica', 
        align:'center', 
        column:"-1,name"
    };
  
    // add the visual DOM elements
    contentCertificate.insertAdjacentHTML('afterbegin', textFieldTemplate)
    let textComponent = contentCertificate.firstChild;	

    // target elements
	const sizeDown = textComponent.querySelector('.size-down');
	const sizeUp = textComponent.querySelector('.size-up');
	const alignLeft = textComponent.querySelector('.align-left');
	const alignCenter = textComponent.querySelector('.align-center');
	const alignRight = textComponent.querySelector('.align-right');
	const columns = textComponent.querySelector('.columns');
	//const familys = textComponent.querySelector('.familys');
	const textFieldDrag = textComponent.querySelector('.drag');
	const textField = textComponent.querySelector('.certificate-field');
	const field = textComponent.querySelector('.field');
	const cancelButton = textComponent.querySelector('.cancel');
	const draggable = textComponent.querySelector('.drag');	

	// put behaviours
	draggable.addEventListener("mousedown", dragStart, false);
	draggable.addEventListener("mouseup", dragEnd, false);
	draggable.addEventListener("mouseleave", dragEnd, false);
	draggable.addEventListener("mousemove", drag, false);
	
	sizeDown.addEventListener('click', changeTextFormat);
	sizeUp.addEventListener('click', changeTextFormat);
	alignLeft.addEventListener('click', changeTextFormat);
	alignCenter.addEventListener('click', changeTextFormat);
	alignRight.addEventListener('click', changeTextFormat);
	columns.addEventListener('change', changeColumn);
	/*familys.addEventListener('change', changeFamily);
	textField.addEventListener('mouseup', onResize);*/
	cancelButton.addEventListener('click', deleteComponent); 

    components.push(component);

    // add the data identifier to the component
	textComponent.dataset.id = components.length - 1;
}

/***  add image component  ***/
const addImage = () => {
    // default image component values
    const component = {
        type:'image',
        width:150,
        height:100,
        x:0, 
        y:36, 
        src:defaultImage
    };

    // add the visual DOM elements to the certificate element
	contentCertificate.insertAdjacentHTML('afterbegin', imageTemplate);
	let imageComponent = contentCertificate.firstChild;

    // target elements
	const cancelButton = imageComponent.querySelector('.cancel');
	const draggable = imageComponent.querySelector('.drag');

    // add behaviours
	cancelButton.addEventListener('click', deleteComponent);
	draggable.addEventListener("mousedown", dragStart, false);
	draggable.addEventListener("mouseup", dragEnd, false);
	draggable.addEventListener("mousemove", drag, false);
	//draggable.parentElement.addEventListener('mouseup', onResize);

    // add the model
    components.push(component);

    // add the data identifier to the component
	imageComponent.dataset.id = components.length - 1;
}

/***  Función para cancelar la creación del certificado  ***/
const actionCancel = () => {
    document.getElementById('field-name').value = '';
    removeBackgroundCertificate();
    components = [];
    
    while( contentCertificate.hasChildNodes() ) {
        contentCertificate.removeChild( contentCertificate.firstChild );
    }
}

// validate name certificate
let errorName = document.getElementById('error-name');
const validateInput = (value) => {
    if ( value ) {
        errorName.style.display = 'none';
        errorName.innerHTML = '';
    } else {
        errorName.style.display = 'block';
        errorName.innerHTML = 'El nombre del certificado es requerido.';
    }
}

/***  Función para guardar los datos del certificado  ***/
const actionSave = () => {
    const nameCertificate = document.getElementById('field-name').value;
    
    if ( !nameCertificate ) {
        errorName.style.display = 'block';
        errorName.innerHTML = 'El nombre del certificado es requerido.';
        return;
    }

    if ( fileBackground ) {
        errorName.style.display = 'none';
        errorName.innerHTML = '';
    } else {
        errorName.style.display = 'block';
        errorName.innerHTML = 'La imagen del certificado es requerida.';
        return;
    }

    const componentsCopy = JSON.stringify(components.filter(Boolean));    

    let dataCertificate = new FormData();
    dataCertificate.append('name_certificate', nameCertificate);
    dataCertificate.append('file', fileBackground);
    dataCertificate.append('components', componentsCopy);
    dataCertificate.append('event_id', event_id);

    $.ajax({
        url: `${baseUrl}/api/v1/save-certificate`,
        type: "POST",
        data: dataCertificate,
        cache: false,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log('Register... ', response);   
            window.close();         
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Error... ',textStatus, errorThrown);
        }
    });
}


// DRAG BEHAVIOUR
function dragStart( e ) {	
	e.preventDefault();
	
	// prevento to drag elements that we does not want to be dragged
	if ( !e.target.classList.contains('drag') ) {
		return;
	}

    active = true;

    // this is the item we are interacting with
    if( e.target.classList.contains('image') ) {
        activeItem = e.target.parentElement.parentElement;
    } else {
        activeItem = e.target.parentElement.parentElement;  
    }
        

    if ( activeItem !== null ) {
        if ( !activeItem.xOffset ) {
            activeItem.xOffset = 0;
        }

        if ( !activeItem.yOffset ) {
            activeItem.yOffset = 0;
        }

        if ( e.type === "touchstart" ) {
            activeItem.initialX = e.touches[0].clientX - activeItem.xOffset;
            activeItem.initialY = e.touches[0].clientY - activeItem.yOffset;
        } else {        
            activeItem.initialX = e.clientX - activeItem.xOffset;
            activeItem.initialY = e.clientY - activeItem.yOffset;
        }
    }
}

function dragEnd(e) {	
	e.stopPropagation();
	
	if ( activeItem !== null ) {
		activeItem.initialX = activeItem.currentX;
		activeItem.initialY = activeItem.currentY;

		// update the values in the model
		components[activeItem.dataset.id].x = activeItem.initialX;
		components[activeItem.dataset.id].y = activeItem.initialY + 36;
	}		

	active = false;
	activeItem = null;
}

function drag(e) {		
	if ( active ) {

		if ( e.type === "touchmove" ) {
			e.preventDefault();
			activeItem.currentX = e.touches[0].clientX - activeItem.initialX;
			activeItem.currentY = e.touches[0].clientY - activeItem.initialY;
		} else {
			activeItem.currentX = e.clientX - activeItem.initialX; 
			activeItem.currentY = e.clientY - activeItem.initialY;
		}		  

		activeItem.xOffset = activeItem.currentX;
		activeItem.yOffset = activeItem.currentY;		  

		setTranslate(activeItem.currentX, activeItem.currentY, activeItem);
	}
}

function setTranslate( xPos, yPos, elem ) {
	elem.style.transform = "translate3d(" + xPos + "px, " + yPos + "px, 0)";
}

// construct the option needed to the columns select element 
const fieldOptionsConstructor = () => {
	let options;

    fields.forEach( item => {
        if ( item.id == -1 ) {
            options = options + `<option value="${item.id},${item.column}">${item.name}</option>`;
        } else {
            options = options + `<option value="${item.id},${item.name}">${item.name}</option>`;
        }
    });
	return options;
}

// change width
function onResize(e){	
    console.log('resize... ', e)
	/* const id = e.target.parentElement.dataset.id;
	components[id].width = e.target.clientWidth;
	if(e.target.parentElement.querySelector('.certificate-image')){
	   components[id].height = e.target.clientHeight;
	}
	console.log(e.target.parentElement.querySelector('.certificate-image')); */
}

// change text format
const changeTextFormat = (e) => {	
    const currentElement = e.currentTarget
    const button = currentElement.name;
	const value = currentElement.value;
	const elementParent = currentElement.parentElement.parentElement
	const textField = elementParent.querySelector('.drag');
	const id = elementParent.dataset.id; 		
	
	// change values	
	switch( button ) {			
		case 'size-up':
		case 'size-down':
			let size = parseInt( textField.style.fontSize );
			size += Number( value );
			components[id].size = size;
			textField.style.fontSize = String(size) + 'px';
			break;
		case 'align-left':
		case 'align-center':
		case 'align-right':
			textField.style.textAlign = components[id].align = value;
			break;
	}
}

// delete component
function deleteComponent(e) {
    const currentElement = e.currentTarget
    const elementParent = currentElement.parentElement.parentElement
	const id = elementParent.dataset.id;    
    const currentImg = components[id];
    let dataDelete = new FormData();
    dataDelete.append('url_img', currentImg.src);
    
    $.ajax({
        url: `${baseUrl}/api/v1/remove-images-certificate`,
        type: "POST",
        data: dataDelete,
        cache: false,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log('Register... ', response);  
            delete components[id]; 
	        elementParent.remove();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Error... ',textStatus, errorThrown);
        }
    });
}

// change column to be rendered
function changeColumn(e){	
    let currentSelect = e.currentTarget;
	const value = currentSelect.value;
	const element = currentSelect.parentElement.parentElement;
	const field = element.querySelector('.field');
	const id = element.dataset.id; 
	
	components[id].column = value;
	field.textContent = '[' + currentSelect.options[currentSelect.selectedIndex].textContent + ']'; 
}

// change family font
/* function changeFamily(e){
		
	const button = e.currentTarget.name;
	const value = e.currentTarget.value;
	const element = e.target.parentElement.parentElement;
	console.log(element);
	const field = element.querySelector('.field');
	const id = element.dataset.id; 

	components[id].fontFamily = value;
	field.style.fontFamily = e.target.options[e.target.selectedIndex].value;
} */

// upload image to the server
const upload = ( event ) => {
    const [ File ] = event.files;
    const apiUrlImg = `${baseUrl}/api/v1/add-images-certificate`;  
    let dataImg = new FormData();
    dataImg.append('file', File)
    dataImg.append('event_id', event_id);
    
    $.ajax({
        url: apiUrlImg,
        type: "POST",
        data: dataImg,
        cache: false,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log('Register... ', response);  
            setUrlImgContent( event, response ) 
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Error... ',textStatus, errorThrown);
        }
    });
}

const setUrlImgContent = ( event, url ) => {
    let urlImg = baseUrl + '/storage' + url;

    const currentElement = event.parentElement.parentElement;
    imageContainer = currentElement.querySelector('.image');
    const id = currentElement.dataset.id;
    components[id].src = url;
    imageContainer.src = urlImg;
}


/***  Template for TextField componet  ***/
const textFieldTemplate = `<div class="draggable" style="width:400px; position:absolute">        
    <div class="certificate-controls">
        
        <button name="size-down" value="-1" type="button" class="btn btn-primary size-down">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-dash-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
            </svg>
        </button>
        <button name="size-up" value="1" type="button" class="btn btn-primary size-up">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
        </button>

        <p style="margin-bottom:2px; padding:4px;">|</p>

        <button name="align-left" value="left" type="button" class="btn btn-primary align-left">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-text-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
            </svg>
        </button>
        <button name="align-center" value="center" type="button" class="btn btn-primary align-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-text-center" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4 12.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
            </svg>
        </button>
        <button name="align-right" value="right" type="button" class="btn btn-primary align-right">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-text-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-4-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm4-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-4-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
            </svg>
        </button>

        <p style="margin-bottom:2px; padding:4px;">|</p>

        <select name="columns" class="form-control columns" style="width: 120px;margin-right: 5px;">
            ${ fieldOptionsConstructor() }				
        </select> 

        <p style="margin-bottom:2px; padding:4px;">|</p>

        <button type="button" class="btn btn-primary cancel">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
            </svg>
        </button>
        
    </div>
    
    <div class="certificate-field">
        <div class="drag" style="font-size:24px;text-align: center;">Texto <span class="field">[Nombre]</span></div>
    </div>				
</div>`;

/* <select name="familys" class="form-control familys" style="width: 120px;">
            <option value="Helvetica">Helvetica</option>
            <option value="Times">Times</option>
            <option value="Courier">Courier</option>				
        </select> */

/***  Template for Image component  ***/
const imageTemplate = `<div class="draggable" style="position:absolute">					
    <div class="certificate-controls">        
        <input name="image" type="file" style="display:none;" onchange="upload(this);"/>

        <button name="edit" type="button" class="btn btn-primary edit" onclick="this.parentElement.querySelector('input').click();">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"></path>
            </svg>
        </button>
       
        <div class="line-divider"></div>

        <button type="button" class="btn btn-primary cancel">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
            </svg>
        </button>
    </div>
            
    <div class="certificate-image">
        <img src="${defaultImage}" class="image drag" style="width: 150px;">        
    </div>
</div>`;
