USE vecinoss;

-- Retira contenido demostrativo de instalaciones anteriores.
DELETE FROM videos
WHERE video_url IN (
  'https://www.youtube.com/watch?v=M7lc1UVf-VE',
  'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
  'https://youtu.be/ScMzIvxBSi4'
);

DELETE FROM posts
WHERE slug IN (
  'vecinos-impulsan-recuperacion-espacios-publicos',
  'municipio-anuncia-mejoras-conectividad',
  'clubes-locales-preparan-encuentros',
  'agenda-cultural-musica-patrimonio',
  'feria-local-oportunidades-emprendedores',
  'recomendaciones-prevenir-emergencias-hogar',
  'juntas-vecinos-actividades-vacaciones-invierno',
  'operativo-servicios-publicos-sectores-rurales',
  'escuelas-deportivas-talleres-gratuitos',
  'biblioteca-local-talleres-encuentros',
  'productores-provincia-venta-directa',
  'simulacro-preventivo-zona-costera',
  'municipio-invita-jornada-familiar-plaza-armas',
  'centro-cultural-talleres-gratuitos',
  'feria-productores-locales-fin-semana',
  'corrida-familiar-convocatoria-todas-edades',
  'bandas-locales-concierto-abierto-anfiteatro',
  'biblioteca-cuentacuentos-actividades-infantiles'
);

-- 20 noticias reales de la Provincia de San Antonio.
-- Los textos son resúmenes originales y cada registro conserva su fuente pública.
INSERT IGNORE INTO posts (category_id,user_id,title,slug,excerpt,body,image,status,featured,published_at)
SELECT c.id,u.id,s.title,s.slug,s.excerpt,s.body,
CASE c.slug
  WHEN 'actualidad-comunal' THEN 'https://images.unsplash.com/photo-1494522358652-f30e61a60313?auto=format&fit=crop&w=1400&q=82'
  WHEN 'cultura' THEN 'https://images.unsplash.com/photo-1488841714725-bb4c32d1ac94?auto=format&fit=crop&w=1400&q=82'
  WHEN 'deportes' THEN 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=1400&q=82'
  WHEN 'salud-y-bienestar' THEN 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1400&q=82'
  WHEN 'emergencias' THEN 'https://images.unsplash.com/photo-1547683905-f686c993aae5?auto=format&fit=crop&w=1400&q=82'
  WHEN 'historias-de-vecinos' THEN 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1400&q=82'
  WHEN 'emprendedores' THEN 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=1400&q=82'
  WHEN 'seguridad' THEN 'https://images.unsplash.com/photo-1453873531674-2151bcd01707?auto=format&fit=crop&w=1400&q=82'
  ELSE 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1400&q=82'
END,'published',0,s.published_at
FROM (
  SELECT 'actualidad-comunal' category_slug,'Biblioteca Vicente Huidobro ampliará sus horarios durante agosto' title,'biblioteca-vicente-huidobro-horarios-agosto-2026' slug,'La biblioteca pública de San Antonio anunció horarios extendidos para que más vecinos puedan utilizar sus espacios durante agosto.' excerpt,'La Biblioteca Pública N°68 Vicente Huidobro informó que durante agosto ofrecerá horarios ampliados para facilitar el acceso de la comunidad a sus salas y servicios. Fuente: https://sanantonio.cl/municipalidad/noticias.html' body,'2026-07-27 15:43:00' published_at
  UNION ALL SELECT 'cultura','San Antonio en Colores vuelve para celebrar el Día de la Niñez','san-antonio-en-colores-dia-ninez-2026','La celebración familiar regresará con actividades recreativas y culturales orientadas a niñas, niños y sus familias.','La Municipalidad anunció el regreso de San Antonio en Colores como parte de la celebración del Día de la Niñez. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-27 15:42:00'
  UNION ALL SELECT 'deportes','Municipio fortalece el arbitraje y el juego limpio en el fútbol amateur','capacitacion-arbitraje-futbol-amateur-san-antonio','Árbitros y participantes del fútbol local accedieron a una jornada formativa para mejorar sus competencias y promover el respeto deportivo.','La iniciativa municipal busca reforzar conocimientos técnicos y fomentar el juego limpio en las competencias amateur de San Antonio. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-27 09:05:00'
  UNION ALL SELECT 'salud-y-bienestar','Equipos de Atención Primaria refuerzan herramientas para cuidar la salud mental','atencion-primaria-salud-mental-san-antonio-2026','Funcionarios de la red comunal participaron en una capacitación destinada a fortalecer el acompañamiento de usuarios y comunidades.','Equipos de Atención Primaria de San Antonio actualizaron herramientas de prevención, detección y cuidado en salud mental. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-27 09:03:00'
  UNION ALL SELECT 'actualidad-comunal','Colegio Agrícola Cuncumén presentó su modelo formativo a autoridad nacional','colegio-agricola-cuncumen-modelo-formativo-2026','La comunidad educativa dio a conocer su trabajo técnico y territorial durante la visita del subsecretario de Agricultura.','El establecimiento mostró su propuesta educativa, su vínculo con el mundo rural y el aprendizaje técnico de sus estudiantes. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-24 12:11:00'
  UNION ALL SELECT 'emergencias','Municipio entrega recomendaciones para enfrentar sistemas frontales','recomendaciones-sistemas-frontales-san-antonio-julio-2026','La autoridad comunal llamó a revisar techumbres, limpiar canaletas y mantenerse informado por canales oficiales.','El municipio difundió medidas preventivas para proteger viviendas y familias frente a lluvias y fuertes vientos. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-23 11:24:00'
  UNION ALL SELECT 'historias-de-vecinos','Viviendas Tuteladas de San Antonio celebran 13 años apoyando a personas mayores','viviendas-tuteladas-san-antonio-13-anos','El programa conmemoró más de una década promoviendo autonomía, acompañamiento y mejor calidad de vida.','El Condominio de Viviendas Tuteladas destacó el trabajo sostenido junto a sus residentes y redes comunitarias. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-22 15:50:00'
  UNION ALL SELECT 'actualidad-comunal','Familias de San Antonio se preparan para la Admisión Escolar 2027','admision-escolar-2027-san-antonio','La comunidad recibió información anticipada sobre las principales etapas del próximo proceso de admisión.','Autoridades locales invitaron a madres, padres y apoderados a revisar con tiempo los requisitos y fechas oficiales. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-22 15:46:00'
  UNION ALL SELECT 'salud-y-bienestar','Nuevo CESFAM 30 de Marzo avanza junto a su comunidad','cesfam-30-de-marzo-avanza-comunidad','El proyecto de infraestructura de salud continúa desarrollándose con participación de vecinos y equipos locales.','La iniciativa busca fortalecer la atención primaria y responder al crecimiento de la demanda en el sector. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-22 13:26:00'
  UNION ALL SELECT 'cultura','Museo de San Antonio recupera sus espacios y prepara su reapertura','museo-san-antonio-recuperacion-reapertura','El Museo de Historia Natural e Histórico trabaja en la recuperación de sus dependencias antes de volver a recibir público.','El equipo del museo avanza en la habilitación de espacios y preparación de contenidos para su próxima reapertura. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-22 12:57:00'
  UNION ALL SELECT 'emergencias','Operativo preventivo vigila el aumento del caudal del estero San Juan','operativo-estero-san-juan-sotero-del-rio','Equipos municipales se desplegaron en Sótero del Río para observar puntos críticos y responder ante posibles emergencias.','La vigilancia se reforzó por el aumento del caudal durante el sistema frontal. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-21 16:05:00'
  UNION ALL SELECT 'actualidad-comunal','Convenio abre nuevas oportunidades de reinserción social juvenil en San Antonio','convenio-reinsercion-social-juvenil-san-antonio','Municipio y servicio especializado acordaron acciones para apoyar a jóvenes en sus procesos de integración social.','El acuerdo busca coordinar programas locales y ampliar oportunidades para jóvenes de la comuna. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-20 15:24:00'
  UNION ALL SELECT 'emergencias','San Antonio evalúa la respuesta comunal tras la primera etapa del temporal','balance-temporal-san-antonio-julio-2026','La municipalidad revisó el despliegue de los equipos y las principales necesidades detectadas durante las lluvias.','El balance destacó la coordinación de cuadrillas y servicios de emergencia en distintos sectores. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-20 15:06:00'
  UNION ALL SELECT 'emprendedores','Emprendedores locales fortalecen sus marcas y estrategias digitales','taller-branding-marketing-emprendedores-san-antonio','Una capacitación municipal entregó herramientas de branding y marketing digital a negocios de la comuna.','La jornada abordó identidad de marca, comunicación y uso de canales digitales para fortalecer emprendimientos locales. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-15 15:31:00'
  UNION ALL SELECT 'emergencias','Más de 26 mil clientes quedaron sin electricidad durante el sistema frontal','cortes-electricidad-provincia-san-antonio-julio-2026','La provincia registró una interrupción masiva del suministro eléctrico durante el paso del temporal.','Datos informados por la Superintendencia de Electricidad y Combustibles mostraron más de 26 mil clientes afectados. Fuente: https://elproa.cl/2026/07/mas-de-26-mil-personas-estuvieron-sin-energia-electrica-durante-este-viernes-en-la-provincia-de-san-antonio/','2026-07-18 12:00:00'
  UNION ALL SELECT 'seguridad','Fiscalización retira vehículos de circulación y cursa múltiples infracciones','fiscalizacion-vehiculos-san-antonio-julio-2026','Un operativo conjunto de seguridad y fiscalización detectó diversas irregularidades en avenida Barros Luco.','Durante la jornada fueron retirados vehículos particulares y se cursaron infracciones. Fuente: https://elproa.cl/2026/07/fiscalizacion-deja-multiples-infracciones-y-vehiculos-retirados-de-circulacion-en-san-antonio/','2026-07-24 10:00:00'
  UNION ALL SELECT 'actualidad-comunal','APR El Convento supera el 75% de avance','apr-el-convento-avance-75-por-ciento','La red de agua potable rural beneficiará a más de tres mil vecinos del sector de Santo Domingo.','El proyecto considera un estanque de 300 metros cúbicos y 27 kilómetros de cañerías. Fuente: https://elproa.cl/','2026-07-24 09:00:00'
  UNION ALL SELECT 'cultura','Seremi de las Culturas visita San Antonio para fortalecer la colaboración local','seremi-culturas-visita-san-antonio-julio-2026','La autoridad regional sostuvo encuentros orientados a promover el intercambio y apoyar el trabajo cultural de la comuna.','La visita consideró conversaciones con actores locales y espacios culturales. Fuente: https://elproa.cl/2026/07/intercambio-y-colaboracion-marcaron-visita-de-la-seremi-de-las-culturas-a-san-antonio/','2026-07-24 08:30:00'
  UNION ALL SELECT 'actualidad-comunal','Puerto San Antonio y Fundación Chile capacitan a más de 500 estudiantes técnicos','puerto-san-antonio-fundacion-chile-estudiantes-tecnicos','Alumnos de nueve liceos técnico-profesionales participan en un programa formativo con apoyo internacional.','La alianza incluye a Fundación Chile y al Instituto Tecnológico de Monterrey para fortalecer la educación técnico-profesional. Fuente: https://www.puertosanantonio.com/puerto-san-antonio-y-fundacion-chile-inician-programa-de-capacitacion-que','2026-07-17 11:00:00'
  UNION ALL SELECT 'actualidad-comunal','Comunidad Logística de San Antonio renueva su alianza con Bomberos','colsa-renueva-alianza-bomberos-san-antonio','Empresas de la comunidad logística reafirmaron su aporte anual al Cuerpo de Bomberos de San Antonio.','El convenio apoya la seguridad y capacidad de respuesta de Bomberos ante emergencias locales. Fuente: https://www.puertosanantonio.com/comunidad-logistica-de-puerto-san-antonio-renueva-historica-alianza-en','2026-07-16 11:00:00'
) s
JOIN categories c ON c.slug=s.category_slug
JOIN users u ON u.email='editor@vecinoss.cl';

-- 20 eventos y actividades reales de la agenda provincial 2026.
INSERT IGNORE INTO posts (category_id,user_id,title,slug,excerpt,body,image,status,featured,published_at)
SELECT c.id,u.id,s.title,s.slug,s.excerpt,s.body,
CASE c.slug
  WHEN 'actividades-culturales' THEN 'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=1400&q=82'
  WHEN 'conciertos' THEN 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1400&q=82'
  WHEN 'eventos-deportivos' THEN 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?auto=format&fit=crop&w=1400&q=82'
  WHEN 'ferias' THEN 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=1400&q=82'
  WHEN 'eventos-municipales' THEN 'https://images.unsplash.com/photo-1517457373958-b7bdd4587205?auto=format&fit=crop&w=1400&q=82'
  ELSE 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1400&q=82'
END,'published',0,s.published_at
FROM (
  SELECT 'actividades-culturales' category_slug,'Fiesta Polifónica del Elenco Coral de San Antonio' title,'evento-fiesta-polifonica-san-antonio-2026' slug,'El elenco coral presentó un concierto gratuito en el Centro Cultural San Antonio.' excerpt,'Actividad cultural confirmada por la Municipalidad de San Antonio. Fuente: https://sanantonio.cl/municipalidad/noticias.html' body,'2026-07-23 19:00:00' published_at
  UNION ALL SELECT 'conciertos','Concierto aniversario de Big Band Puerto San Antonio','evento-big-band-puerto-san-antonio-23-anos','La agrupación celebró 23 años de trayectoria con un concierto gratuito para la comunidad.','Actividad realizada en el Centro Cultural San Antonio. Fuente: https://www.youtube.com/watch?v=yqQ0ax5I72A','2026-07-25 19:00:00'
  UNION ALL SELECT 'actividades-culturales','Kubrick y Nolan protagonizan ciclo de cine de julio','evento-ciclo-cine-kubrick-nolan-san-antonio','El ciclo municipal programó películas de dos directores fundamentales del cine contemporáneo.','Programación publicada por la Municipalidad de San Antonio. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-10 18:00:00'
  UNION ALL SELECT 'actividades-culturales','San Antonio en Colores celebra el Día de la Niñez','evento-san-antonio-en-colores-dia-ninez','Jornada familiar con actividades artísticas, recreativas y comunitarias.','Actividad anunciada por la Municipalidad de San Antonio. Fuente: https://sanantonio.cl/municipalidad/noticias.html','2026-07-27 15:42:00'
  UNION ALL SELECT 'eventos-deportivos','Corrida Familiar por el 70° aniversario de El Quisco','evento-corrida-familiar-el-quisco-2026','La Plaza Yungay será el punto de encuentro para una jornada deportiva abierta a la comunidad.','Evento confirmado para el 1 de agosto de 2026. Fuente: https://corre.cl/evento/12171','2026-07-28 09:30:00'
  UNION ALL SELECT 'ferias','III Festival Gastronómico del Caldillo de Congrio','evento-festival-caldillo-congrio-el-quisco-2026','El Quisco prepara una nueva edición de su encuentro gastronómico dedicado al caldillo de congrio.','La actividad y convocatoria de stands fueron publicadas por la Municipalidad de El Quisco. Fuente: https://www.elquisco.cl/','2026-07-28 10:00:00'
  UNION ALL SELECT 'conciertos','Luis Hachem se presenta en San Antonio','evento-luis-hachem-san-antonio-agosto-2026','Presentación programada en Araukasa Sanguches & Bar de San Antonio.','Evento confirmado para el 8 de agosto de 2026 a las 21:00. Fuente: https://www.passline.com/eventos/luis-hachem-en-san-antonio','2026-07-28 11:00:00'
  UNION ALL SELECT 'conciertos','Marejada 2026 presenta showcases en el Centro Cultural','evento-marejada-showcases-san-antonio-2026','Encuentro musical programado en el Centro Cultural San Antonio.','Actividad anunciada para el 21 de agosto de 2026 a las 18:30. Fuente: https://www.portaldisc.com/cartelera/ccsanantonio','2026-07-28 12:00:00'
  UNION ALL SELECT 'actividades-culturales','Tour guiado por Playa Fósil de Algarrobo','evento-tour-playa-fosil-algarrobo-2026','Recorrido patrimonial y natural por uno de los puntos de interés de la comuna.','Actividad registrada en la agenda local de Algarrobo. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-07-11 13:45:00'
  UNION ALL SELECT 'eventos-municipales','Mini bingo solidario del Grupo Anticheo','evento-mini-bingo-solidario-algarrobo-2026','Encuentro comunitario organizado en el Club Deportivo Algarrobo.','Actividad registrada en la agenda local. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-07-05 16:00:00'
  UNION ALL SELECT 'ferias','Arte y Música en el Pueblo de Artesanos','evento-arte-musica-pueblo-artesanos-algarrobo-2026','Fin de semana de creación local, música y encuentro con artesanos de Algarrobo.','Actividad registrada en la agenda local. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-07-04 12:00:00'
  UNION ALL SELECT 'eventos-municipales','Fiesta de San Pedro en Algarrobo','evento-fiesta-san-pedro-algarrobo-2026','La comuna honró al patrono de los pescadores con una tradicional celebración costera.','Actividad realizada en la Capilla San Pedro. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-06-26 10:00:00'
  UNION ALL SELECT 'actividades-culturales','Vacaciones de invierno en el Pueblo de los Artesanos','evento-vacaciones-invierno-artesanos-algarrobo-2026','Programación familiar y cultural desarrollada durante las vacaciones escolares.','Actividad registrada en la agenda de Algarrobo. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-06-26 09:30:00'
  UNION ALL SELECT 'ferias','Expo Emprendedores Invierno 2026 en Algarrobo','evento-expo-emprendedores-invierno-algarrobo','Emprendedores locales exhibieron productos y servicios en Plaza Bordemar.','Actividad registrada en la agenda de Algarrobo. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-06-22 11:00:00'
  UNION ALL SELECT 'eventos-deportivos','Rugby playa se toma Algarrobo','evento-rugby-playa-algarrobo-2026','Jornada deportiva frente al mar con participación de equipos y comunidad.','Actividad realizada en Playa Pejerrey. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-06-13 10:00:00'
  UNION ALL SELECT 'actividades-culturales','Cine al aire libre con clásicos chilenos','evento-cine-aire-libre-algarrobo-2026','La Plaza de Algarrobo recibió una función gratuita de cine nacional.','Actividad registrada en la agenda local. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-06-13 20:00:00'
  UNION ALL SELECT 'ferias','Feria costumbrista de Algarrobo','evento-feria-costumbrista-algarrobo-2026','Gastronomía, oficios y productos locales se reunieron en la costanera.','Actividad registrada en la agenda de Algarrobo. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-06-13 11:00:00'
  UNION ALL SELECT 'conciertos','Trío Litoral ofrece música en vivo en Algarrobo','evento-trio-litoral-algarrobo-2026','Presentación musical desarrollada en Casa Dominga.','Actividad registrada en la agenda local de Algarrobo. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-06-11 20:30:00'
  UNION ALL SELECT 'actividades-culturales','Caminata guiada por El Yeco','evento-caminata-el-yeco-algarrobo-2026','Recorrido guiado por el entorno natural de la Reserva El Yeco.','Actividad registrada en la agenda local de Algarrobo. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-06-11 11:00:00'
  UNION ALL SELECT 'ferias','Feria de artesanos del litoral','evento-feria-artesanos-litoral-algarrobo-2026','Artesanos de la zona se reunieron en la Plaza de Armas de Algarrobo.','Actividad registrada en la agenda local. Fuente: https://algarrobo.cl/eventos-panoramas-actividades-en-algarrobo','2026-06-11 10:00:00'
) s
JOIN categories c ON c.slug=s.category_slug
JOIN users u ON u.email='editor@vecinoss.cl';

-- 20 videos públicos reales. La URL de portada utiliza la miniatura oficial de YouTube.
INSERT INTO videos (title,description,commune,format,cover_image,video_url,status,published_at)
SELECT s.title,s.description,s.commune,s.format,CONCAT('https://img.youtube.com/vi/',s.youtube_id,'/hqdefault.jpg'),CONCAT('https://www.youtube.com/watch?v=',s.youtube_id),'published',s.published_at
FROM (
  SELECT 'Reconstrucción del Hogar de Niñas de San Antonio' title,'Informe sobre las gestiones para reconstruir el recinto afectado.' description,'San Antonio' commune,'Reportajes' format,'rnbieq7j12s' youtube_id,'2026-07-22 12:00:00' published_at
  UNION ALL SELECT 'Pescadores enfrentan el mal tiempo y la amenaza de flotas extranjeras','Testimonios y situación de la pesca artesanal local.','San Antonio','Reportajes','k_8s5WLPb64','2026-07-23 12:00:00'
  UNION ALL SELECT 'Alcalde entrega balance tras intensas lluvias y fuertes vientos','Balance comunal de las emergencias provocadas por el temporal.','San Antonio','Entrevistas','7Tap3CQmE7o','2026-07-20 12:00:00'
  UNION ALL SELECT 'Elenco coral presenta concierto gratuito en San Antonio','Invitación y detalles de la presentación coral.','San Antonio','Entrevistas','LtKI__xLElk','2026-07-23 13:00:00'
  UNION ALL SELECT 'Municipio refuerza vigilancia ante aumento de caudales','Despliegue preventivo en sectores expuestos a crecidas.','San Antonio','Reportajes','ErSGls3VilI','2026-07-21 12:00:00'
  UNION ALL SELECT 'San Antonio coordina medidas ante nuevos sistemas frontales','Autoridades explican el trabajo preventivo comunal.','San Antonio','Entrevistas','1qe1A2cX5p8','2026-07-13 12:00:00'
  UNION ALL SELECT 'Temporal deja fuertes imágenes en San Antonio','Recorrido audiovisual por distintos puntos afectados.','San Antonio','Crónicas','8CIGlsaKZNM','2026-07-21 13:00:00'
  UNION ALL SELECT 'Big Band Puerto San Antonio celebra 23 años','Entrevista a Patricio Baos sobre la historia y el concierto aniversario.','San Antonio','Entrevistas','yqQ0ax5I72A','2026-07-22 14:00:00'
  UNION ALL SELECT 'Balance de las emergencias provocadas por el temporal','El periodista Pablo Medina recorre sectores afectados de San Antonio.','San Antonio','Reportajes','GK1oWoaXNF8','2026-07-17 12:00:00'
  UNION ALL SELECT 'Tardanza en suspender clases genera cuestionamientos','Revisión de las decisiones tomadas durante el segundo sistema frontal.','San Antonio','Reportajes','Rbk4wKgtlGM','2026-07-22 15:00:00'
  UNION ALL SELECT 'Crédito CAF permitirá iniciar trabajos de Puerto Exterior','Detalles del financiamiento por 50 millones de dólares.','San Antonio','Reportajes','pjsyGkm0tkA','2026-06-19 12:00:00'
  UNION ALL SELECT 'Informe propone postergar la construcción del Puerto Exterior','Análisis de las razones planteadas en el informe.','San Antonio','Reportajes','epsQEotrNoU','2026-07-20 16:00:00'
  UNION ALL SELECT 'SEA recomienda aprobar el estudio ambiental de Puerto Exterior','Cobertura del avance ambiental del proyecto portuario.','San Antonio','Reportajes','NqU-43ck59Y','2026-05-18 12:00:00'
  UNION ALL SELECT 'Puerto San Antonio desde el aire en mayo de 2026','Registro audiovisual panorámico de las operaciones portuarias.','San Antonio','Crónicas','ZHmFQoJ6xdo','2026-05-18 13:00:00'
  UNION ALL SELECT 'Puerto de San Antonio: registro del 27 de junio','Transmisión y vista de la actividad marítima del principal puerto del país.','San Antonio','Transmisiones en vivo','d18Rw8CN2sc','2026-06-27 12:00:00'
  UNION ALL SELECT 'Dirigentes participan en lanzamiento de Fondos Concursables 2026','Representantes sociales conocen las líneas de apoyo de Puerto San Antonio.','San Antonio','Crónicas','sVrY5awm3d8','2026-07-10 12:00:00'
  UNION ALL SELECT 'Panoramas de invierno en el Centro Cultural San Antonio','Revisión de la cartelera cultural para las vacaciones de invierno.','San Antonio','Entrevistas','LsNn2h9tPSg','2026-06-26 12:00:00'
  UNION ALL SELECT 'Pescadores de San Antonio preparan la celebración de San Pedro','La comunidad pesquera comparte detalles de su tradicional festividad.','San Antonio','Entrevistas','BrdEDlXHM7U','2026-06-25 12:00:00'
  UNION ALL SELECT 'Albergues de invierno funcionarán hasta octubre en San Antonio','Información sobre la atención de personas en situación de calle.','San Antonio','Reportajes','ZH227hQpk-0','2026-06-19 13:00:00'
  UNION ALL SELECT 'El Quisco despliega operativo ante efectos del sistema frontal','Equipos municipales recorren sectores afectados y coordinan ayudas.','El Quisco','Reportajes','bMvI2c5DAWk','2026-07-23 14:00:00'
) s
WHERE NOT EXISTS (SELECT 1 FROM videos v WHERE v.video_url=CONCAT('https://www.youtube.com/watch?v=',s.youtube_id));
