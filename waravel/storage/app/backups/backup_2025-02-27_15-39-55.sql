--
-- PostgreSQL database dump
--

-- Dumped from database version 17.3 (Debian 17.3-1.pgdg120+1)
-- Dumped by pg_dump version 17.2 (Ubuntu 17.2-1.pgdg24.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: public; Type: SCHEMA; Schema: -; Owner: sail
--

-- *not* creating schema, since initdb creates it


ALTER SCHEMA public OWNER TO sail;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: sail
--

COMMENT ON SCHEMA public IS '';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO sail;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO sail;

--
-- Name: cliente_favoritos; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.cliente_favoritos (
    id bigint NOT NULL,
    cliente_id bigint NOT NULL,
    producto_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.cliente_favoritos OWNER TO sail;

--
-- Name: cliente_favoritos_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.cliente_favoritos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cliente_favoritos_id_seq OWNER TO sail;

--
-- Name: cliente_favoritos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.cliente_favoritos_id_seq OWNED BY public.cliente_favoritos.id;


--
-- Name: clientes; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.clientes (
    id bigint NOT NULL,
    guid character varying(255) NOT NULL,
    nombre character varying(255) NOT NULL,
    apellido character varying(255) NOT NULL,
    avatar character varying(255) NOT NULL,
    telefono character varying(255) NOT NULL,
    direccion json NOT NULL,
    activo boolean DEFAULT true NOT NULL,
    usuario_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.clientes OWNER TO sail;

--
-- Name: clientes_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.clientes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.clientes_id_seq OWNER TO sail;

--
-- Name: clientes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.clientes_id_seq OWNED BY public.clientes.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO sail;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO sail;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO sail;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO sail;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO sail;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO sail;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO sail;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO sail;

--
-- Name: productos; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.productos (
    id bigint NOT NULL,
    guid character varying(255) NOT NULL,
    vendedor_id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    descripcion text NOT NULL,
    "estadoFisico" character varying(255) NOT NULL,
    precio numeric(8,2) NOT NULL,
    stock integer NOT NULL,
    categoria character varying(255) NOT NULL,
    estado character varying(255) NOT NULL,
    imagenes json NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT productos_categoria_check CHECK (((categoria)::text = ANY (ARRAY[('Tecnologia'::character varying)::text, ('Ropa'::character varying)::text, ('Hogar'::character varying)::text, ('Coleccionismo'::character varying)::text, ('Vehiculos'::character varying)::text, ('Videojuegos'::character varying)::text, ('Musica'::character varying)::text, ('Deporte'::character varying)::text, ('Cine'::character varying)::text, ('Cocina'::character varying)::text]))),
    CONSTRAINT "productos_estadoFisico_check" CHECK ((("estadoFisico")::text = ANY (ARRAY[('Nuevo'::character varying)::text, ('Usado'::character varying)::text, ('Deteriorado'::character varying)::text]))),
    CONSTRAINT productos_estado_check CHECK (((estado)::text = ANY (ARRAY[('Disponible'::character varying)::text, ('Vendido'::character varying)::text, ('Desactivado'::character varying)::text, ('Baneado'::character varying)::text])))
);


ALTER TABLE public.productos OWNER TO sail;

--
-- Name: productos_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.productos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.productos_id_seq OWNER TO sail;

--
-- Name: productos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.productos_id_seq OWNED BY public.productos.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO sail;

--
-- Name: users; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    guid character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    role character varying(255) DEFAULT 'user'::character varying NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    password_reset_token character varying(255),
    password_reset_expires_at timestamp(0) without time zone,
    CONSTRAINT users_role_check CHECK (((role)::text = ANY (ARRAY[('user'::character varying)::text, ('cliente'::character varying)::text, ('admin'::character varying)::text])))
);


ALTER TABLE public.users OWNER TO sail;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO sail;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: valoraciones; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.valoraciones (
    id bigint NOT NULL,
    guid character varying(255) NOT NULL,
    comentario character varying(255) NOT NULL,
    puntuacion integer NOT NULL,
    "clienteValorado_id" bigint NOT NULL,
    autor_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.valoraciones OWNER TO sail;

--
-- Name: valoraciones_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.valoraciones_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.valoraciones_id_seq OWNER TO sail;

--
-- Name: valoraciones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.valoraciones_id_seq OWNED BY public.valoraciones.id;


--
-- Name: ventas; Type: TABLE; Schema: public; Owner: sail
--

CREATE TABLE public.ventas (
    id bigint NOT NULL,
    guid character varying(255) NOT NULL,
    comprador json NOT NULL,
    "lineaVentas" json NOT NULL,
    "precioTotal" double precision NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ventas OWNER TO sail;

--
-- Name: ventas_id_seq; Type: SEQUENCE; Schema: public; Owner: sail
--

CREATE SEQUENCE public.ventas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ventas_id_seq OWNER TO sail;

--
-- Name: ventas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sail
--

ALTER SEQUENCE public.ventas_id_seq OWNED BY public.ventas.id;


--
-- Name: cliente_favoritos id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.cliente_favoritos ALTER COLUMN id SET DEFAULT nextval('public.cliente_favoritos_id_seq'::regclass);


--
-- Name: clientes id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.clientes ALTER COLUMN id SET DEFAULT nextval('public.clientes_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: productos id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.productos ALTER COLUMN id SET DEFAULT nextval('public.productos_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: valoraciones id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.valoraciones ALTER COLUMN id SET DEFAULT nextval('public.valoraciones_id_seq'::regclass);


--
-- Name: ventas id; Type: DEFAULT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ventas ALTER COLUMN id SET DEFAULT nextval('public.ventas_id_seq'::regclass);


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: cliente_favoritos; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.cliente_favoritos (id, cliente_id, producto_id, created_at, updated_at) FROM stdin;
1	1	1	2025-02-26 18:54:54	2025-02-26 18:54:54
2	1	5	2025-02-26 18:54:54	2025-02-26 18:54:54
3	2	2	2025-02-26 18:54:54	2025-02-26 18:54:54
4	2	6	2025-02-26 18:54:54	2025-02-26 18:54:54
5	3	3	2025-02-26 18:54:54	2025-02-26 18:54:54
6	3	4	2025-02-26 18:54:54	2025-02-26 18:54:54
\.


--
-- Data for Name: clientes; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.clientes (id, guid, nombre, apellido, avatar, telefono, direccion, activo, usuario_id, created_at, updated_at) FROM stdin;
1	de6a7d01-af1d-44fb-a615-2583f52da3c4	Juan	Perez	clientes/avatar.png	612345678	{"calle":"Avenida Siempre Viva","numero":742,"piso":1,"letra":"A","codigoPostal":28001}	t	1	2025-02-26 18:54:54	2025-02-26 18:54:54
2	97b37979-552a-4916-989a-b96031348856	Maria	Garcia	clientes/avatar.png	987654321	{"calle":"Calle de las Nubes","numero":123,"piso":2,"letra":"C","codigoPostal":28971}	t	2	2025-02-26 18:54:54	2025-02-26 18:54:54
3	81f58779-2a53-4922-b628-16c330d4b28a	Pedro	Martinez	clientes/avatar.png	321456789	{"calle":"Avenida Espa\\u00f1a","numero":456,"piso":3,"letra":"B","codigoPostal":28970}	f	3	2025-02-26 18:54:54	2025-02-26 18:54:54
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2025_02_14_165112_create_clientes_table	1
5	2025_02_14_165136_create_productos_table	1
6	2025_02_14_165313_create_valoraciones_table	1
7	2025_02_15_134258_create_cliente_favoritos_table	1
8	2025_02_17_191112_create_venta_table	1
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: productos; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.productos (id, guid, vendedor_id, nombre, descripcion, "estadoFisico", precio, stock, categoria, estado, imagenes, created_at, updated_at) FROM stdin;
1	1f0368b0-5ce9-4099-bd13-0c1cede8d349	1	Portátil Gamer	Este potente portátil está diseñado para gaming de alto rendimiento y tareas exigentes como edición de video y modelado 3D. Equipado con un procesador de última generación, tarjeta gráfica dedicada y una pantalla de alta frecuencia de actualización, ofrece una experiencia fluida tanto para jugadores como para profesionales.	Nuevo	800.00	100	Tecnologia	Disponible	["productos\\/portatil1.webp","productos\\/portatil2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
2	b4cb952d-1568-4763-901a-dfb9e05a4992	2	Chaqueta de Cuero	Chaqueta de cuero genuino con un diseño clásico y sofisticado. Ideal para quienes buscan un look elegante sin sacrificar comodidad y protección contra el frío. Su forro interior aporta calidez, mientras que su material resistente garantiza una larga durabilidad.	Usado	15.00	100	Ropa	Disponible	["productos\\/chaqueta1.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
3	8b09d444-554b-4b95-9962-d6d64d46719c	3	Guitarra Eléctrica	Guitarra eléctrica de cuerpo sólido con un diseño clásico y un sonido potente. Perfecta para músicos de cualquier nivel que buscan un instrumento versátil para rock, blues, jazz y más. Aunque presenta signos de uso, su sonido sigue siendo excepcional y está lista para conectar y tocar.	Deteriorado	300.00	100	Musica	Disponible	["productos\\/guitarra1.webp","productos\\/guitarra2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
4	f047b42f-5151-4497-a300-7e78794c850f	1	Pantalones Vaqueros	Pantalones vaqueros de alta calidad, confeccionados con tela resistente y un ajuste cómodo. Ideales para el día a día o para combinarlos con distintos estilos. Su diseño clásico nunca pasa de moda y su durabilidad los hace una opción excelente para cualquier guardarropa.	Nuevo	10.00	100	Ropa	Disponible	["productos\\/pantalones1.webp","productos\\/pantalones2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
5	b060d2c6-9386-46a7-88c8-607850d75585	2	Mario Party 8	Divertido juego de fiesta para toda la familia. Mario Party 8 ofrece una gran variedad de minijuegos y tableros interactivos que garantizan horas de entretenimiento. Perfecto para jugar solo o en compañía de amigos y familiares, este título es un clásico de la saga de Nintendo.	Nuevo	50.00	100	Videojuegos	Disponible	["productos\\/mario1.webp","productos\\/mario2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
6	00f26236-958a-4043-8756-60430d98040d	3	Consola Xbox Series X	La consola de nueva generación de Microsoft con un rendimiento excepcional. Disfruta de gráficos en 4K, tiempos de carga ultrarrápidos y una biblioteca de juegos extensa. Ideal para quienes buscan la mejor experiencia en videojuegos y entretenimiento en casa.	Nuevo	250.00	100	Videojuegos	Disponible	["productos\\/xbox1.webp","productos\\/xbox2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
7	a7f42c1b-3d5f-4a8d-9e79-2d3a87eb8c12	2	Set de Púas para Guitarra	Paquete de 12 púas de diferentes grosores y materiales, ideales para distintos estilos musicales. Perfectas para guitarras acústicas y eléctricas.	Nuevo	5.00	100	Musica	Disponible	["productos\\/puas1.webp","productos\\/puas2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
8	b3d62a5c-83f7-47dc-b02a-98e5f8a28e9d	2	Amplificador Fender 40W	Amplificador Fender de 40W con ecualización ajustable y efectos de reverberación. Ideal para ensayos y pequeñas presentaciones en vivo.	Usado	120.00	100	Musica	Disponible	["productos\\/ampli1.webp","productos\\/ampli2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
9	cf89d4b6-276f-4f15-bd16-77c531dc1b3d	1	Batería Electrónica Roland	Kit de batería electrónica con pads sensibles al tacto, módulo de sonidos y conexión MIDI para grabaciones digitales.	Nuevo	600.00	100	Musica	Disponible	["productos\\/bateria1.webp","productos\\/bateria2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
10	d2f86a6a-89c3-4e1a-84b2-0d8473a2b4e1	3	Smartwatch Xiaomi Mi Band 7	Reloj inteligente con monitor de actividad física, sensor de frecuencia cardíaca y notificaciones de smartphone.	Nuevo	50.00	100	Tecnologia	Disponible	["productos\\/smartwatch1.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
11	e91a8d56-5eb6-43b5-b847-b9d35ef0d30e	3	Zapatillas Adidas Running	Zapatillas deportivas con suela de espuma de alto rendimiento. Comodidad y soporte ideal para correr largas distancias.	Nuevo	70.00	100	Ropa	Disponible	["productos\\/zapatillas1.webp","productos\\/zapatillas2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
12	f4c1d6e3-69bf-4a17-9c8e-1bd48b92b25e	2	Lámpara LED Inteligente	Lámpara de escritorio con luz LED regulable y control táctil. Compatible con asistentes de voz como Alexa y Google Home.	Nuevo	30.00	100	Hogar	Disponible	["productos\\/lampara1.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
13	09c7a5f9-30b7-470a-98c1-22c3c7b6e5fd	1	The Legend of Zelda: Breath of the Wild	Juego de aventura y exploración para Nintendo Switch con un mundo abierto enorme y mecánicas innovadoras.	Nuevo	55.00	100	Videojuegos	Disponible	["productos\\/zelda1.webp","productos\\/zelda2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
14	b5a36f42-44f8-4e6d-8f14-1c3a98c5f9d7	1	Colección de Blu-ray Star Wars	Edición especial en Blu-ray de la saga completa de Star Wars, con contenido exclusivo y material detrás de cámaras.	Usado	80.00	100	Cine	Disponible	["productos\\/starwars1.webp","productos\\/starwars2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
15	d7e41a94-8a8f-48dc-96e6-62b8c839f27b	3	Cafetera Espresso Automática	Cafetera con molinillo integrado y sistema de espumado de leche. Perfecta para preparar café de calidad en casa.	Nuevo	150.00	100	Cocina	Disponible	["productos\\/cafetera1.webp","productos\\/cafetera2.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
16	c8f972b6-3f44-4b0f-8e6f-7a5c961f9b3d	1	Figura de Acción Spider-Man	Figura coleccionable de Spider-Man en edición especial con detalles realistas y articulaciones móviles.	Nuevo	40.00	100	Coleccionismo	Disponible	["productos\\/spiderman1.webp"]	2025-02-26 18:54:54	2025-02-26 18:54:54
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
9jRqWXAJhKIrK3j8YGFQ9W9hTtIZm642AfCDwc1e	\N	172.18.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36	YTozOntzOjY6Il90b2tlbiI7czo0MDoiUnYwNFFXeTBXUFlCN1pjWjBFTlBXZTFrQkc4M3llN29rQUdiYWtVZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTY6Imh0dHA6Ly9sb2NhbGhvc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1740596171
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.users (id, guid, name, email, email_verified_at, password, role, remember_token, created_at, updated_at, password_reset_token, password_reset_expires_at) FROM stdin;
1	789a7609-624a-49b2-bcf9-a9ea1d034f5e	Admin	admin@example.com	2025-02-26 18:54:53	$2y$12$rKuAtOWto.VP/DzloeXjrOhjSOFgFvhvzzfREmtL0U.FTSD9BK4aS	admin		2025-02-26 18:54:53	2025-02-26 18:54:53	\N	\N
2	2491f841-0993-4096-82b9-6884a887f683	Juan Pérez	juan@example.com	2025-02-26 18:54:53	$2y$12$Xpa66xpVyKG25GMkewJl0uONUUsKyx/nhkW4KOIkj1ybSE1etaoEu	cliente		2025-02-26 18:54:53	2025-02-26 18:54:53	\N	\N
3	3ce8a699-56cb-4765-acb2-2b5e36fea78f	María García	maria@example.com	2025-02-26 18:54:54	$2y$12$LOEQyy7nLTUzOOmWnUbL8Of9UGrf1KDrrLKrtTWyHwamR4kZitMcq	cliente		2025-02-26 18:54:54	2025-02-26 18:54:54	\N	\N
4	5852148c-4d79-4556-a20f-9448b6d55279	Pedro Martínez	pedro@example.com	2025-02-26 18:54:54	$2y$12$hmYz2W3R/h2RAsxZglxbPudJLYcfLBUhkRMSgJWkor0V4u9U71cn6	cliente		2025-02-26 18:54:54	2025-02-26 18:54:54	\N	\N
5	9d00acfc-64b4-4406-9de9-5988aa3e4816	Isabella Rodríguez	isabella@example.com	2025-02-26 18:54:54	$2y$12$vaYTEyNgjJO/YS5ACOiaReUDqoOF3OImqgCHR09AtLCes4iKj2aja	user		2025-02-26 18:54:54	2025-02-26 18:54:54	\N	\N
\.


--
-- Data for Name: valoraciones; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.valoraciones (id, guid, comentario, puntuacion, "clienteValorado_id", autor_id, created_at, updated_at) FROM stdin;
1	1a2b3c4d-1234-5678-9abc-def012345678	Excelente vendedor, muy amable.	5	2	3	2025-02-26 18:54:54	2025-02-26 18:54:54
2	2b3c4d5e-2345-6789-abcd-ef1234567890	El producto llegó en buen estado, recomendado.	4	3	1	2025-02-26 18:54:54	2025-02-26 18:54:54
3	3c4d5e6f-3456-789a-bcde-f23456789012	No me gustó el trato, esperaba más comunicación.	2	1	2	2025-02-26 18:54:54	2025-02-26 18:54:54
4	4d5e6f7g-4567-89ab-cdef-345678901234	Muy buen servicio, repetiré compra.	5	2	1	2025-02-26 18:54:54	2025-02-26 18:54:54
5	5e6f7g8h-5678-9abc-def0-456789012345	Producto con detalles, pero buen vendedor.	3	2	3	2025-02-26 18:54:54	2025-02-26 18:54:54
\.


--
-- Data for Name: ventas; Type: TABLE DATA; Schema: public; Owner: sail
--

COPY public.ventas (id, guid, comprador, "lineaVentas", "precioTotal", created_at, updated_at) FROM stdin;
1	0fe3fd18-a0f6-4139-99be-6e1dad2cd420	{"id":2,"guid":"97b37979-552a-4916-989a-b96031348856","nombre":"Maria","apellido":"Garcia"}	[{"vendedor":{"id":1,"guid":"de6a7d01-af1d-44fb-a615-2583f52da3c4","nombre":"Juan","apellido":"Perez"},"cantidad":2,"producto":{"id":1,"guid":"1f0368b0-5ce9-4099-bd13-0c1cede8d349","nombre":"Portatil Gamer","descripcion":"Portatil gaming de gama alta para trabajos pesados.","estadoFisico":"Nuevo","precio":800,"categoria":"Tecnologia"},"precioTotal":1600}]	1600	2025-02-26 18:54:54	2025-02-26 18:54:54
2	8345f27b-20b0-4a14-9889-58797c991925	{"id":3,"guid":"81f58779-2a53-4922-b628-16c330d4b28a","nombre":"Pedro","apellido":"Martinez"}	[{"vendedor":{"id":1,"guid":"de6a7d01-af1d-44fb-a615-2583f52da3c4","nombre":"Juan","apellido":"Perez"},"cantidad":1,"producto":{"id":4,"guid":"f047b42f-5151-4497-a300-7e78794c850f","nombre":"Pantalones de lana","descripcion":"Pantalones de lana de manga corta y c\\u00f3modos.","estadoFisico":"Nuevo","precio":10,"categoria":"Ropa"},"precioTotal":10},{"vendedor":{"id":2,"guid":"97b37979-552a-4916-989a-b96031348856","nombre":"Maria","apellido":"Garcia"},"cantidad":2,"producto":{"id":5,"guid":"b060d2c6-9386-46a7-88c8-607850d75585","nombre":"Mario Party 8","descripcion":"Juego de plataformas y acci\\u00f3n, muy popular en Nintendo.","estadoFisico":"Nuevo","precio":50,"categoria":"Videojuegos"},"precioTotal":100}]	110	2025-02-26 18:54:54	2025-02-26 18:54:54
\.


--
-- Name: cliente_favoritos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.cliente_favoritos_id_seq', 6, true);


--
-- Name: clientes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.clientes_id_seq', 3, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.migrations_id_seq', 8, true);


--
-- Name: productos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.productos_id_seq', 16, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.users_id_seq', 5, true);


--
-- Name: valoraciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.valoraciones_id_seq', 5, true);


--
-- Name: ventas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sail
--

SELECT pg_catalog.setval('public.ventas_id_seq', 2, true);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: cliente_favoritos cliente_favoritos_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.cliente_favoritos
    ADD CONSTRAINT cliente_favoritos_pkey PRIMARY KEY (id);


--
-- Name: clientes clientes_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: productos productos_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: valoraciones valoraciones_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.valoraciones
    ADD CONSTRAINT valoraciones_pkey PRIMARY KEY (id);


--
-- Name: ventas ventas_pkey; Type: CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.ventas
    ADD CONSTRAINT ventas_pkey PRIMARY KEY (id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: sail
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: cliente_favoritos cliente_favoritos_cliente_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.cliente_favoritos
    ADD CONSTRAINT cliente_favoritos_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES public.clientes(id) ON DELETE CASCADE;


--
-- Name: cliente_favoritos cliente_favoritos_producto_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.cliente_favoritos
    ADD CONSTRAINT cliente_favoritos_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES public.productos(id) ON DELETE CASCADE;


--
-- Name: clientes clientes_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: productos productos_vendedor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_vendedor_id_foreign FOREIGN KEY (vendedor_id) REFERENCES public.clientes(id) ON DELETE CASCADE;


--
-- Name: valoraciones valoraciones_autor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.valoraciones
    ADD CONSTRAINT valoraciones_autor_id_foreign FOREIGN KEY (autor_id) REFERENCES public.clientes(id) ON DELETE CASCADE;


--
-- Name: valoraciones valoraciones_clientevalorado_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: sail
--

ALTER TABLE ONLY public.valoraciones
    ADD CONSTRAINT valoraciones_clientevalorado_id_foreign FOREIGN KEY ("clienteValorado_id") REFERENCES public.clientes(id) ON DELETE CASCADE;


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: sail
--

REVOKE USAGE ON SCHEMA public FROM PUBLIC;


--
-- PostgreSQL database dump complete
--

