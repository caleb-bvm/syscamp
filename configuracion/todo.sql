
CREATE DATABASE bootcampst13
    WITH
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'en-US'
    LC_CTYPE = 'en-US'
    LOCALE_PROVIDER = 'libc'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1
    IS_TEMPLATE = False;


CREATE TABLE IF NOT EXISTS public.departamento
(
    id_departamento integer NOT NULL DEFAULT nextval('departamento_id_departamento_seq'::regclass),
    codigo_departamento integer NOT NULL,
    nombre_departamento text COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT departamento_pkey PRIMARY KEY (id_departamento),
    CONSTRAINT departamento_codigo_departamento_key UNIQUE (codigo_departamento),
    CONSTRAINT departamento_nombre_departamento_key UNIQUE (nombre_departamento)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.departamento
    OWNER to postgres;


CREATE TABLE IF NOT EXISTS public.distrito
(
    id_distrito integer NOT NULL DEFAULT nextval('distrito_id_distrito_seq'::regclass),
    id_municipio integer NOT NULL,
    codigo_distrito integer NOT NULL,
    nombre_distrito text COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT distrito_pkey PRIMARY KEY (id_distrito),
    CONSTRAINT distrito_codigo_distrito_key UNIQUE (codigo_distrito),
    CONSTRAINT distrito_id_municipio_fkey FOREIGN KEY (id_municipio)
        REFERENCES public.municipio (id_municipio) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.distrito
    OWNER to postgres;


CREATE TABLE IF NOT EXISTS public.institucion
(
    id_institucion integer NOT NULL DEFAULT nextval('institucion_id_institucion_seq'::regclass),
    id_distrito integer NOT NULL,
    codigo_de_infraestructura integer NOT NULL,
    nombre_institucion text COLLATE pg_catalog."default" NOT NULL,
    sector_institucion text COLLATE pg_catalog."default" NOT NULL,
    zona_institucion text COLLATE pg_catalog."default" NOT NULL,
    longitud_institucion double precision,
    latitud_institucion double precision,
    CONSTRAINT institucion_pkey PRIMARY KEY (id_institucion),
    CONSTRAINT institucion_codigo_de_infraestructura_key UNIQUE (codigo_de_infraestructura),
    CONSTRAINT institucion_id_distrito_fkey FOREIGN KEY (id_distrito)
        REFERENCES public.distrito (id_distrito) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT institucion_sector_institucion_check CHECK (sector_institucion = 'público'::text OR sector_institucion = 'privado'::text),
    CONSTRAINT institucion_zona_institucion_check CHECK (zona_institucion = 'urbana'::text OR zona_institucion = 'rural'::text)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.institucion
    OWNER to postgres;


CREATE TABLE IF NOT EXISTS public.municipio
(
    id_municipio integer NOT NULL DEFAULT nextval('municipio_id_municipio_seq'::regclass),
    id_departamento integer NOT NULL,
    codigo_municipio integer NOT NULL,
    nombre_municipio text COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT municipio_pkey PRIMARY KEY (id_municipio),
    CONSTRAINT municipio_codigo_municipio_key UNIQUE (codigo_municipio),
    CONSTRAINT municipio_nombre_municipio_key UNIQUE (nombre_municipio),
    CONSTRAINT municipio_id_departamento_fkey FOREIGN KEY (id_departamento)
        REFERENCES public.departamento (id_departamento) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.municipio
    OWNER to postgres;


CREATE TABLE IF NOT EXISTS public.persona
(
    id_persona integer NOT NULL DEFAULT nextval('persona_id_persona_seq'::regclass),
    id_rol integer NOT NULL,
    codigo_persona text COLLATE pg_catalog."default" NOT NULL,
    correo_persona text COLLATE pg_catalog."default" NOT NULL,
    clave_persona text COLLATE pg_catalog."default" NOT NULL,
    nombre_persona text COLLATE pg_catalog."default" NOT NULL,
    apellido_persona text COLLATE pg_catalog."default" NOT NULL,
    documento_de_identificacion text COLLATE pg_catalog."default" NOT NULL,
    id_distrito_reside integer NOT NULL,
    id_departamento_labora integer NOT NULL,
    username character varying(50) COLLATE pg_catalog."default",
    CONSTRAINT persona_pkey PRIMARY KEY (id_persona),
    CONSTRAINT persona_codigo_persona_key UNIQUE (codigo_persona),
    CONSTRAINT persona_correo_persona_key UNIQUE (correo_persona),
    CONSTRAINT persona_documento_de_identificacion_key UNIQUE (documento_de_identificacion),
    CONSTRAINT persona_id_departamento_labora_fkey FOREIGN KEY (id_departamento_labora)
        REFERENCES public.departamento (id_departamento) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT persona_id_distrito_reside_fkey FOREIGN KEY (id_distrito_reside)
        REFERENCES public.distrito (id_distrito) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT persona_id_rol_fkey FOREIGN KEY (id_rol)
        REFERENCES public.rol (id_rol) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.persona
    OWNER to postgres;


CREATE TABLE IF NOT EXISTS public.preguntas
(
    cod_pregunta integer NOT NULL DEFAULT nextval('preguntas_cod_pregunta_seq'::regclass),
    pregunta text COLLATE pg_catalog."default",
    categoria character varying(50) COLLATE pg_catalog."default",
    CONSTRAINT preguntas_pkey PRIMARY KEY (cod_pregunta)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.preguntas
    OWNER to postgres;


CREATE TABLE IF NOT EXISTS public.respuestas
(
    cod_respuesta integer NOT NULL DEFAULT nextval('respuestas_cod_respuesta_seq'::regclass),
    id_institucion integer,
    codigo_persona integer,
    grado character varying(50) COLLATE pg_catalog."default",
    seccion character varying(50) COLLATE pg_catalog."default",
    turno character varying(50) COLLATE pg_catalog."default",
    cantidad_estudiantes smallint,
    fecha date,
    CONSTRAINT respuestas_pkey PRIMARY KEY (cod_respuesta),
    CONSTRAINT respuestas_id_institucion_fkey FOREIGN KEY (id_institucion)
        REFERENCES public.institucion (id_institucion) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.respuestas
    OWNER to postgres;


CREATE TABLE IF NOT EXISTS public.respuestas_detalladas
(
    cod_detalle bigint NOT NULL GENERATED ALWAYS AS IDENTITY ( INCREMENT 1 START 1 MINVALUE 1 MAXVALUE 9223372036854775807 CACHE 1 ),
    respuestas_cod_respuesta integer,
    cod_pregunta smallint,
    respuesta character varying(2) COLLATE pg_catalog."default",
    comentario text COLLATE pg_catalog."default",
    CONSTRAINT respuestas_detalladas_pkey PRIMARY KEY (cod_detalle),
    CONSTRAINT respuestas_detalladas_cod_pregunta_fkey FOREIGN KEY (cod_pregunta)
        REFERENCES public.preguntas (cod_pregunta) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT respuestas_detalladas_respuestas_cod_respuesta_fkey FOREIGN KEY (respuestas_cod_respuesta)
        REFERENCES public.respuestas (cod_respuesta) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.respuestas_detalladas
    OWNER to postgres;


CREATE TABLE IF NOT EXISTS public.rol
(
    id_rol integer NOT NULL DEFAULT nextval('rol_id_rol_seq'::regclass),
    nombre_rol text COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT rol_pkey PRIMARY KEY (id_rol),
    CONSTRAINT rol_nombre_rol_key UNIQUE (nombre_rol)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.rol
    OWNER to postgres;


CREATE TABLE IF NOT EXISTS public.seccion_instrumento
(
    id_seccion_instrumento bigint NOT NULL DEFAULT nextval('seccion_instrumento_id_seccion_instrumento_seq'::regclass),
    id_instrumento bigint NOT NULL,
    orden_seccion_instrumento integer NOT NULL,
    nombre_seccion_instrumento text COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT seccion_instrumento_pkey PRIMARY KEY (id_seccion_instrumento)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.seccion_instrumento
    OWNER to postgres;


CREATE TABLE IF NOT EXISTS public.visita
(
    id_visita bigint NOT NULL DEFAULT nextval('visita_id_visita_seq'::regclass),
    id_persona integer NOT NULL,
    id_institucion integer NOT NULL,
    turno_visita text COLLATE pg_catalog."default" NOT NULL,
    grado_visita text COLLATE pg_catalog."default",
    seccion_visita text COLLATE pg_catalog."default",
    cantidad_estudiantes_visita integer NOT NULL,
    observacion_visita text COLLATE pg_catalog."default",
    fecha_visita date NOT NULL,
    CONSTRAINT visita_pkey PRIMARY KEY (id_visita),
    CONSTRAINT visita_id_institucion_fkey FOREIGN KEY (id_institucion)
        REFERENCES public.institucion (id_institucion) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT visita_id_persona_fkey FOREIGN KEY (id_persona)
        REFERENCES public.persona (id_persona) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT visita_turno_visita_check CHECK (turno_visita = 'matutino'::text OR turno_visita = 'vespertino'::text OR turno_visita = 'nocturno'::text),
    CONSTRAINT visita_cantidad_estudiantes_visita_check CHECK (cantidad_estudiantes_visita >= 0)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.visita
    OWNER to postgres;