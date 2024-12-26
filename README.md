<h1>SEO ALT Images Plugin</h1>

<p><strong>SEO Images Plugin</strong> es un plugin para WordPress diseñado para ayudar a mejorar el SEO de tu sitio web asegurándose de que todas las imágenes en las entradas y páginas tengan el atributo <code>alt</code> correctamente configurado. El atributo <code>alt</code> es esencial para mejorar la accesibilidad del sitio y también tiene un impacto positivo en el SEO al describir el contenido de las imágenes para los motores de búsqueda.</p>

<h2>Características</h2>
<ul>
        <li>Detecta imágenes en entradas y páginas sin el atributo <code>alt</code>.</li>
        <li>Genera automáticamente el atributo <code>alt</code> utilizando el título de la entrada o página.</li>
        <li>Actualiza el contenido de las entradas y páginas directamente desde el panel de administración de WordPress.</li>
        <li>Interfaz sencilla para visualizar imágenes sin atributo <code>alt</code> y actualizarlas fácilmente.</li>
</ul>

<h2>Instalación</h2>
<p>Para instalar el plugin en tu sitio de WordPress:</p>
<ol>
        <li>Descarga el archivo del plugin y descomprímelo.</li>
        <li>Sube la carpeta del plugin a la carpeta <code>wp-content/plugins</code> de tu instalación de WordPress.</li>
        <li>Activa el plugin desde el panel de administración de WordPress, en <strong>Plugins > Plugins instalados</strong>.</li>
</ol>

<h2>Uso</h2>
<p>Una vez que el plugin esté instalado y activado, podrás acceder a su página desde el menú de administración de WordPress:</p>
<ul>
        <li>Ve a <strong>SEO Images</strong> en el menú del administrador.</li>
        <li>El plugin mostrará una lista de imágenes que no tienen el atributo <code>alt</code> en las entradas y páginas publicadas.</li>
        <li>Si se encuentran imágenes sin <code>alt</code>, podrás hacer clic en el botón <strong>Generar ALT</strong> para generar automáticamente el atributo <code>alt</code> utilizando el título de la entrada o página.</li>
</ul>

<h2>Descripción técnica</h2>
<p>Este plugin realiza una consulta a la base de datos para obtener todas las entradas y páginas publicadas. Luego, analiza el contenido de cada entrada/página buscando imágenes sin el atributo <code>alt</code>. Si se encuentra una imagen sin este atributo, el plugin generará un valor para el atributo <code>alt</code> utilizando el título de la entrada o página donde se encuentra la imagen.</p>

<h2>Compatibilidad Wordpress</h2>
<p>Este plugin ha sido probado en la rama 6.x de Wordpress</p>