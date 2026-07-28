CREATE DATABASE IF NOT EXISTS vecinoss CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vecinoss;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  description VARCHAR(255),
  parent_id BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_categories_parent (parent_id),
  CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE posts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(180) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  excerpt VARCHAR(350),
  body LONGTEXT NOT NULL,
  image VARCHAR(500),
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  featured TINYINT(1) NOT NULL DEFAULT 0,
  published_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_posts_public (status, published_at),
  INDEX idx_posts_category (category_id),
  CONSTRAINT fk_posts_category FOREIGN KEY (category_id) REFERENCES categories(id),
  CONSTRAINT fk_posts_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE tags (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE post_tags (
  post_id BIGINT UNSIGNED NOT NULL,
  tag_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (post_id, tag_id),
  CONSTRAINT fk_post_tags_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  CONSTRAINT fk_post_tags_tag FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE settings (
  setting_key VARCHAR(100) PRIMARY KEY,
  setting_value TEXT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE videos (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  description LONGTEXT,
  commune VARCHAR(40) NOT NULL DEFAULT 'San Antonio',
  format VARCHAR(40) NOT NULL DEFAULT 'Reportajes',
  cover_image VARCHAR(500),
  video_url VARCHAR(1000) NOT NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  published_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_videos_public (status, published_at)
) ENGINE=InnoDB;

INSERT INTO videos (title,description,cover_image,video_url,status,published_at) VALUES
('Video de muestra: YouTube','Contenido demostrativo para revisar el reproductor de VecinoSS TV con un enlace estándar de YouTube.','https://img.youtube.com/vi/M7lc1UVf-VE/hqdefault.jpg','https://www.youtube.com/watch?v=M7lc1UVf-VE','published',DATE_SUB(NOW(),INTERVAL 1 HOUR)),
('Video de muestra: archivo multimedia','Ejemplo audiovisual que permite comprobar la portada, el título y la reproducción desde la sección principal.','https://img.youtube.com/vi/aqz-KE-bpKQ/hqdefault.jpg','https://www.youtube.com/watch?v=aqz-KE-bpKQ','published',DATE_SUB(NOW(),INTERVAL 2 HOUR)),
('Video de muestra: enlace corto de YouTube','Ejemplo de una URL corta compatible con el reproductor integrado y administrable desde el panel.','https://img.youtube.com/vi/ScMzIvxBSi4/hqdefault.jpg','https://youtu.be/ScMzIvxBSi4','published',DATE_SUB(NOW(),INTERVAL 3 HOUR));

INSERT INTO users (name,email,password) VALUES
('Equipo VecinoSS','editor@vecinoss.cl','$2y$12$zLgzc4.Y2YWVesK5mWEXm.tEgWNvFp6dza.J8to/eMozWi112YH5.');

INSERT INTO categories (id,name,slug,description,parent_id) VALUES
(1,'Noticias','noticias','La actualidad de la Provincia de San Antonio.',NULL),(2,'Comunidad','comunidad','Historias y voces de nuestros barrios.',NULL),(3,'VecinoSS TV','vecinoss-tv','Contenido audiovisual local.',NULL),(4,'Emprendedores y Guía Local','guia-local','Directorio comercial y turístico.',NULL),(5,'Eventos','eventos','Agenda provincial de actividades.',NULL),
(10,'Actualidad comunal','actualidad-comunal',NULL,1),(11,'Emergencias','emergencias',NULL,1),(12,'Seguridad','seguridad',NULL,1),(13,'Política local','politica-local',NULL,1),(14,'Deportes','deportes',NULL,1),(15,'Cultura','cultura',NULL,1),
(20,'Denuncias Ciudadanas','denuncias-ciudadanas',NULL,2),(21,'Mascotas perdidas y adopciones','mascotas',NULL,2),(22,'Historias de vecinos','historias-de-vecinos',NULL,2),(23,'Salud y bienestar','salud-y-bienestar',NULL,2),(24,'Consejos prácticos','consejos-practicos',NULL,2),(25,'El Consejo del Vecino','consejo-del-vecino',NULL,2),
(40,'Emprendedores','emprendedores',NULL,4),(41,'Turismo','turismo',NULL,4),(42,'Restaurantes','restaurantes',NULL,4),(43,'Servicios','servicios',NULL,4),(44,'Ferias','ferias-locales',NULL,4),(45,'Comercios','comercios',NULL,4),
(50,'Eventos municipales','eventos-municipales',NULL,5),(51,'Actividades culturales','actividades-culturales',NULL,5),(52,'Ferias','ferias',NULL,5),(53,'Deportes','eventos-deportivos',NULL,5),(54,'Conciertos','conciertos',NULL,5);

INSERT INTO posts (category_id,user_id,title,slug,excerpt,body,image,status,featured,published_at) VALUES
(20,1,'Vecinos impulsan una nueva jornada de recuperación de espacios públicos','vecinos-impulsan-recuperacion-espacios-publicos','Organizaciones locales convocaron a familias y voluntarios para renovar una de las plazas más concurridas de la comuna.','La comunidad volvió a demostrar que el trabajo colaborativo puede transformar los barrios. Desde temprano, vecinas y vecinos se reunieron para limpiar, pintar y recuperar las áreas verdes de la plaza.\n\nLa actividad contó con la participación de organizaciones sociales, emprendedores y familias del sector. Sus organizadores adelantaron que la iniciativa se repetirá en otros puntos de la provincia.','https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&w=1400&q=80','published',1,NOW()),
(10,1,'Municipio anuncia mejoras de conectividad para distintos sectores','municipio-anuncia-mejoras-conectividad','El plan considera nuevas obras y coordinación con las comunidades durante las próximas semanas.','Las autoridades dieron a conocer un calendario de trabajos que busca mejorar los tiempos de traslado y la seguridad vial. Los detalles serán comunicados a cada sector antes del inicio de las obras.','https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 1 HOUR)),
(14,1,'Clubes locales preparan sus próximos encuentros deportivos','clubes-locales-preparan-encuentros','Las series juveniles y adultas afinan los últimos detalles para una fecha llena de actividad.','Los recintos de la provincia recibirán una nueva jornada deportiva. Los clubes invitaron a la comunidad a acompañar a sus equipos y disfrutar en familia.','https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 2 HOUR)),
(15,1,'Agenda cultural reúne música, patrimonio y actividades familiares','agenda-cultural-musica-patrimonio','Conoce los panoramas gratuitos que se realizarán este fin de semana en la provincia.','Diversos espacios culturales abrirán sus puertas con una programación para todas las edades. La agenda incluye música en vivo, recorridos patrimoniales y talleres.','https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 3 HOUR)),
(40,1,'Feria local abre nuevas oportunidades para emprendedores','feria-local-oportunidades-emprendedores','Productores y comercios de la zona mostrarán sus propuestas en una nueva feria abierta a la comunidad.','La actividad busca fortalecer la economía local y conectar directamente a emprendedores con sus clientes. Habrá gastronomía, artesanía y servicios.','https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 4 HOUR)),
(11,1,'Refuerzan recomendaciones para prevenir emergencias en el hogar','recomendaciones-prevenir-emergencias-hogar','Equipos especializados compartieron consejos prácticos y canales de atención.','La prevención comienza con acciones sencillas. Las autoridades recomendaron revisar instalaciones, mantener despejadas las vías de evacuación y tener visibles los números de emergencia.','https://images.unsplash.com/photo-1452421822248-d4c2b47f0c81?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 5 HOUR));

INSERT INTO posts (category_id,user_id,title,slug,excerpt,body,image,status,featured,published_at) VALUES
(20,1,'Juntas de vecinos coordinan actividades para las vacaciones de invierno','juntas-vecinos-actividades-vacaciones-invierno','Talleres, juegos y encuentros comunitarios serán parte de la programación para niñas, niños y familias.','Las organizaciones territoriales prepararon un calendario colaborativo de actividades gratuitas. La información de horarios y lugares será difundida a través de sus canales comunitarios.','https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 6 HOUR)),
(10,1,'Nuevo operativo acercará servicios públicos a sectores rurales','operativo-servicios-publicos-sectores-rurales','La jornada permitirá realizar consultas y recibir orientación sin trasladarse al centro de la comuna.','El operativo reunirá equipos de distintas instituciones para facilitar trámites y responder consultas de las comunidades rurales de la provincia.','https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 7 HOUR)),
(14,1,'Escuelas deportivas abren inscripciones para nuevos talleres gratuitos','escuelas-deportivas-talleres-gratuitos','La oferta incluye disciplinas individuales y colectivas para diferentes edades.','Las inscripciones se realizarán durante toda la semana. Los cupos son limitados y buscan promover la actividad física y la vida saludable.','https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 8 HOUR)),
(15,1,'Biblioteca local renueva su cartelera de talleres y encuentros','biblioteca-local-talleres-encuentros','La programación contempla clubes de lectura, actividades infantiles y espacios para autores de la zona.','La biblioteca invitó a toda la comunidad a revisar su nueva programación mensual y participar en actividades abiertas y gratuitas.','https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 9 HOUR)),
(40,1,'Productores de la provincia fortalecen sus canales de venta directa','productores-provincia-venta-directa','Una nueva red permitirá conocer y adquirir productos elaborados en el territorio.','La iniciativa reúne a productores de distintas comunas y busca dar visibilidad al trabajo local mediante ferias, catálogos y puntos de venta directa.','https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 10 HOUR)),
(11,1,'Equipos de emergencia realizan simulacro preventivo en zona costera','simulacro-preventivo-zona-costera','El ejercicio permitió revisar protocolos, rutas de evacuación y coordinación institucional.','Vecinos, establecimientos y equipos de respuesta participaron en un ejercicio destinado a fortalecer la preparación ante emergencias.','https://images.unsplash.com/photo-1584467735871-8297329d3228?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 11 HOUR));

INSERT INTO posts (category_id,user_id,title,slug,excerpt,body,image,status,featured,published_at) VALUES
(50,1,'Municipio invita a jornada familiar en la Plaza de Armas','municipio-invita-jornada-familiar-plaza-armas','Música, juegos y servicios municipales formarán parte de una tarde abierta a toda la comunidad.','La jornada reunirá actividades recreativas para todas las edades y puntos de información de los principales servicios municipales. La entrada es liberada.','https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 12 HOUR)),
(51,1,'Centro cultural abre inscripciones para talleres gratuitos','centro-cultural-talleres-gratuitos','La programación incluye fotografía, teatro y pintura para jóvenes y personas adultas.','Las inscripciones se realizarán durante la semana en el centro cultural. Los talleres cuentan con cupos limitados y materiales incluidos.','https://images.unsplash.com/photo-1549490349-8643362247b5?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 13 HOUR)),
(52,1,'Feria de productores locales llega este fin de semana','feria-productores-locales-fin-semana','Emprendedores de la provincia ofrecerán alimentos, artesanía y productos elaborados en el territorio.','La feria funcionará durante todo el fin de semana y contará con espacios de descanso, gastronomía y presentaciones para las familias.','https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 14 HOUR)),
(53,1,'Corrida familiar abre convocatoria para todas las edades','corrida-familiar-convocatoria-todas-edades','El encuentro deportivo contempla circuitos recreativos y competitivos por el borde costero.','La organización habilitó categorías infantiles, juveniles y adultas. La inscripción es gratuita y se recomienda asistir con ropa deportiva.','https://images.unsplash.com/photo-1552674605-db6ffd4facb5?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 15 HOUR)),
(54,1,'Bandas locales protagonizan concierto abierto en el anfiteatro','bandas-locales-concierto-abierto-anfiteatro','Una selección de artistas de la zona presentará sus nuevos trabajos en una jornada gratuita.','El concierto comenzará durante la tarde y reunirá distintos estilos musicales. La actividad es abierta a la comunidad y apta para todo público.','https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 16 HOUR)),
(51,1,'Biblioteca prepara cuentacuentos y actividades infantiles','biblioteca-cuentacuentos-actividades-infantiles','Niñas, niños y sus familias podrán participar en una mañana dedicada a la lectura.','La actividad incluye narración de cuentos, juegos creativos y un espacio para conocer las novedades de la biblioteca local. No requiere inscripción.','https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=900&q=80','published',0,DATE_SUB(NOW(),INTERVAL 17 HOUR));
