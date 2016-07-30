--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner:
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: adminpack; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION IF NOT EXISTS adminpack WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION adminpack; Type: COMMENT; Schema: -; Owner:
--

COMMENT ON EXTENSION adminpack IS 'administrative functions for PostgreSQL';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: address_lookup; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE address_lookup (
    id integer NOT NULL,
    provider text NOT NULL,
    reference text,
    region text NOT NULL,
    postcode text NOT NULL,
    number integer NOT NULL,
    street text,
    city text,
    state text,
    country text,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.address_lookup OWNER TO "apiUser";

--
-- Name: address_lookup_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE address_lookup_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.address_lookup_id_seq OWNER TO "apiUser";

--
-- Name: address_lookup_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE address_lookup_id_seq OWNED BY address_lookup.id;


--
-- Name: applications; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE applications (
    id integer NOT NULL,
    company_id integer NOT NULL,
    provider text NOT NULL,
    token bytea NOT NULL,
    secret bytea NOT NULL,
    version text,
    enabled boolean DEFAULT true NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.applications OWNER TO "apiUser";

--
-- Name: applications_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE applications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.applications_id_seq OWNER TO "apiUser";

--
-- Name: applications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE applications_id_seq OWNED BY applications.id;


--
-- Name: attributes; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE attributes (
    id integer NOT NULL,
    identity_id integer NOT NULL,
    name text NOT NULL,
    value bytea,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.attributes OWNER TO "apiUser";

--
-- Name: attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.attributes_id_seq OWNER TO "apiUser";

--
-- Name: attributes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE attributes_id_seq OWNED BY attributes.id;


--
-- Name: city_list; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE city_list (
    id integer NOT NULL,
    name text NOT NULL,
    alternate_name text,
    region text,
    country text
);


ALTER TABLE public.city_list OWNER TO "apiUser";

--
-- Name: city_list_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE city_list_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.city_list_id_seq OWNER TO "apiUser";

--
-- Name: city_list_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE city_list_id_seq OWNED BY city_list.id;


--
-- Name: companies; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE companies (
    id integer NOT NULL,
    name text NOT NULL,
    public_key text NOT NULL,
    private_key bytea NOT NULL,
    personal boolean DEFAULT false NOT NULL,
    parent_id integer,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.companies OWNER TO "apiUser";

--
-- Name: companies_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE companies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.companies_id_seq OWNER TO "apiUser";

--
-- Name: companies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE companies_id_seq OWNED BY companies.id;


--
-- Name: company_process_handlers; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE company_process_handlers (
    id integer NOT NULL,
    handler_id integer NOT NULL,
    company_id integer NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.company_process_handlers OWNER TO "apiUser";

--
-- Name: company_process_handlers_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE company_process_handlers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.company_process_handlers_id_seq OWNER TO "apiUser";

--
-- Name: company_process_handlers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE company_process_handlers_id_seq OWNED BY company_process_handlers.id;


--
-- Name: company_service_handlers; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE company_service_handlers (
    id integer NOT NULL,
    handler_id integer NOT NULL,
    company_id integer NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.company_service_handlers OWNER TO "apiUser";

--
-- Name: company_service_handlers_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE company_service_handlers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.company_service_handlers_id_seq OWNER TO "apiUser";

--
-- Name: company_service_handlers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE company_service_handlers_id_seq OWNED BY company_service_handlers.id;


--
-- Name: company_whitelist; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE company_whitelist (
    id integer NOT NULL,
    company_id integer NOT NULL,
    fqdn boolean DEFAULT false NOT NULL,
    value bytea NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.company_whitelist OWNER TO "apiUser";

--
-- Name: company_whitelist_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE company_whitelist_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.company_whitelist_id_seq OWNER TO "apiUser";

--
-- Name: company_whitelist_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE company_whitelist_id_seq OWNED BY company_whitelist.id;


--
-- Name: controls; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE controls (
    id integer NOT NULL,
    user_id integer NOT NULL,
    scrape integer DEFAULT (-1) NOT NULL,
    map integer DEFAULT (-1) NOT NULL,
    feature integer DEFAULT (-1) NOT NULL,
    score integer DEFAULT (-1) NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.controls OWNER TO "apiUser";

--
-- Name: controls_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE controls_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.controls_id_seq OWNER TO "apiUser";

--
-- Name: controls_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE controls_id_seq OWNED BY controls.id;


--
-- Name: country_list; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE country_list (
    code character(2) NOT NULL,
    name text NOT NULL
);


ALTER TABLE public.country_list OWNER TO "apiUser";

--
-- Name: credential_whitelist; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE credential_whitelist (
    id integer NOT NULL,
    credential_id integer NOT NULL,
    fqdn boolean DEFAULT false NOT NULL,
    value bytea NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.credential_whitelist OWNER TO "apiUser";

--
-- Name: credential_whitelist_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE credential_whitelist_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.credential_whitelist_id_seq OWNER TO "apiUser";

--
-- Name: credential_whitelist_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE credential_whitelist_id_seq OWNED BY credential_whitelist.id;


--
-- Name: credentials; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE credentials (
    id integer NOT NULL,
    company_id integer NOT NULL,
    name text NOT NULL,
    public text NOT NULL,
    private bytea NOT NULL,
    production boolean DEFAULT false NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.credentials OWNER TO "apiUser";

--
-- Name: credentials_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE credentials_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.credentials_id_seq OWNER TO "apiUser";

--
-- Name: credentials_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE credentials_id_seq OWNED BY credentials.id;


--
-- Name: digested; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE digested (
    id integer NOT NULL,
    source_id integer NOT NULL,
    name text NOT NULL,
    value bytea,
    created timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.digested OWNER TO "apiUser";

--
-- Name: digested_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE digested_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.digested_id_seq OWNER TO "apiUser";

--
-- Name: digested_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE digested_id_seq OWNED BY digested.id;


--
-- Name: email; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE email (
    id integer NOT NULL,
    source_id integer NOT NULL,
    email bytea NOT NULL,
    code text NOT NULL,
    verified boolean DEFAULT false NOT NULL,
    expires timestamp without time zone DEFAULT now() NOT NULL,
    ipaddr text,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.email OWNER TO "apiUser";

--
-- Name: email_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE email_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.email_id_seq OWNER TO "apiUser";

--
-- Name: email_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE email_id_seq OWNED BY email.id;


--
-- Name: features; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE features (
    id integer NOT NULL,
    identity_id integer NOT NULL,
    name text NOT NULL,
    value bytea,
    created timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.features OWNER TO "apiUser";

--
-- Name: features_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE features_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.features_id_seq OWNER TO "apiUser";

--
-- Name: features_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE features_id_seq OWNED BY features.id;


--
-- Name: flags; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE flags (
    id integer NOT NULL,
    identity_id integer NOT NULL,
    name text NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.flags OWNER TO "apiUser";

--
-- Name: flags_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE flags_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.flags_id_seq OWNER TO "apiUser";

--
-- Name: flags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE flags_id_seq OWNED BY flags.id;


--
-- Name: gates; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE gates (
    id integer NOT NULL,
    identity_id integer NOT NULL,
    name text NOT NULL,
    pass boolean DEFAULT false NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.gates OWNER TO "apiUser";

--
-- Name: gates_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE gates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.gates_id_seq OWNER TO "apiUser";

--
-- Name: gates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE gates_id_seq OWNED BY gates.id;


--
-- Name: hook_errors; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE hook_errors (
    id integer NOT NULL,
    hook_id integer NOT NULL,
    payload bytea NOT NULL,
    error bytea,
    created timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.hook_errors OWNER TO "apiUser";

--
-- Name: hook_errors_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE hook_errors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.hook_errors_id_seq OWNER TO "apiUser";

--
-- Name: hook_errors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE hook_errors_id_seq OWNED BY hook_errors.id;


--
-- Name: hooks; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE hooks (
    id integer NOT NULL,
    credential_id integer NOT NULL,
    trigger text NOT NULL,
    url bytea NOT NULL,
    subscribed boolean DEFAULT true NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.hooks OWNER TO "apiUser";

--
-- Name: hooks_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE hooks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.hooks_id_seq OWNER TO "apiUser";

--
-- Name: hooks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE hooks_id_seq OWNED BY hooks.id;


--
-- Name: identities; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE identities (
    id integer NOT NULL,
    public_key text NOT NULL,
    private_key bytea NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.identities OWNER TO "apiUser";

--
-- Name: identities_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE identities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.identities_id_seq OWNER TO "apiUser";

--
-- Name: identities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE identities_id_seq OWNED BY identities.id;


--
-- Name: known_name_list; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE known_name_list (
    name text NOT NULL,
    type text NOT NULL,
    soundex text NOT NULL,
    metaphone text NOT NULL,
    dmetaphone1 text NOT NULL,
    dmetaphone2 text NOT NULL
);


ALTER TABLE public.known_name_list OWNER TO "apiUser";

--
-- Name: logs; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE logs (
    id integer NOT NULL,
    company_id integer NOT NULL,
    credential_id integer NOT NULL,
    user_id integer,
    level text NOT NULL,
    message text NOT NULL,
    context bytea,
    created timestamp without time zone DEFAULT now() NOT NULL,
    ipaddr text
);


ALTER TABLE public.logs OWNER TO "apiUser";

--
-- Name: logs_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.logs_id_seq OWNER TO "apiUser";

--
-- Name: logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE logs_id_seq OWNED BY logs.id;

--
-- Name: metrics; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE metrics (
    id integer NOT NULL,
    name text NOT NULL,
    value real NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.metrics OWNER TO "apiUser";

--
-- Name: metrics_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE metrics_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.metrics_id_seq OWNER TO "apiUser";

--
-- Name: metrics_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE metrics_id_seq OWNED BY metrics.id;


--
-- Name: name_list; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE name_list (
    country text NOT NULL,
    name text NOT NULL,
    gender character(1) NOT NULL,
    soundex text NOT NULL,
    metaphone text NOT NULL,
    dmetaphone1 text NOT NULL,
    dmetaphone2 text NOT NULL
);


ALTER TABLE public.name_list OWNER TO "apiUser";

--
-- Name: normalised; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE normalised (
    id integer NOT NULL,
    source_id integer NOT NULL,
    name text NOT NULL,
    value bytea,
    created timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.normalised OWNER TO "apiUser";

--
-- Name: normalised_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE normalised_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.normalised_id_seq OWNER TO "apiUser";

--
-- Name: normalised_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE normalised_id_seq OWNED BY normalised.id;


--
-- Name: phinxlog; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE phinxlog (
    version bigint NOT NULL,
    migration_name character varying(100),
    start_time timestamp without time zone NOT NULL,
    end_time timestamp without time zone NOT NULL
);


ALTER TABLE public.phinxlog OWNER TO "apiUser";

--
-- Name: process_handlers; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE process_handlers (
    id integer NOT NULL,
    process_id integer NOT NULL,
    company_id integer,
    name text NOT NULL,
    step text NOT NULL,
    sources text,
    runlevel integer DEFAULT 0 NOT NULL,
    location text NOT NULL
);


ALTER TABLE public.process_handlers OWNER TO "apiUser";

--
-- Name: process_handlers_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE process_handlers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.process_handlers_id_seq OWNER TO "apiUser";

--
-- Name: process_handlers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE process_handlers_id_seq OWNED BY process_handlers.id;


--
-- Name: processes; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE processes (
    id integer NOT NULL,
    name text NOT NULL,
    enabled boolean DEFAULT true NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.processes OWNER TO "apiUser";

--
-- Name: processes_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE processes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.processes_id_seq OWNER TO "apiUser";

--
-- Name: processes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE processes_id_seq OWNED BY processes.id;


--
-- Name: role_access; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE role_access (
    id integer NOT NULL,
    identity_id integer NOT NULL,
    role text NOT NULL,
    resource text,
    access boolean DEFAULT true NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.role_access OWNER TO "apiUser";

--
-- Name: role_access_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE role_access_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.role_access_id_seq OWNER TO "apiUser";

--
-- Name: role_access_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE role_access_id_seq OWNED BY role_access.id;


--
-- Name: scores; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE scores (
    id integer NOT NULL,
    attribute_id integer NOT NULL,
    name text NOT NULL,
    value real DEFAULT 0 NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.scores OWNER TO "apiUser";

--
-- Name: scores_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE scores_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.scores_id_seq OWNER TO "apiUser";

--
-- Name: scores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE scores_id_seq OWNED BY scores.id;


--
-- Name: service_handlers; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE service_handlers (
    id integer NOT NULL,
    service_id integer NOT NULL,
    company_id integer,
    name text NOT NULL,
    source text NOT NULL,
    location text NOT NULL
);


ALTER TABLE public.service_handlers OWNER TO "apiUser";

--
-- Name: service_handlers_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE service_handlers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.service_handlers_id_seq OWNER TO "apiUser";

--
-- Name: service_handlers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE service_handlers_id_seq OWNED BY service_handlers.id;


--
-- Name: services; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE services (
    id integer NOT NULL,
    name text NOT NULL,
    enabled boolean DEFAULT true NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.services OWNER TO "apiUser";

--
-- Name: services_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE services_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.services_id_seq OWNER TO "apiUser";

--
-- Name: services_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE services_id_seq OWNED BY services.id;


--
-- Name: settings; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE settings (
    id integer NOT NULL,
    company_id integer NOT NULL,
    category text NOT NULL,
    property text NOT NULL,
    value bytea NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.settings OWNER TO "apiUser";

--
-- Name: settings_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.settings_id_seq OWNER TO "apiUser";

--
-- Name: settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE settings_id_seq OWNED BY settings.id;


--
-- Name: sms; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE sms (
    id integer NOT NULL,
    source_id integer NOT NULL,
    phone bytea NOT NULL,
    code text NOT NULL,
    verified boolean DEFAULT false NOT NULL,
    expires timestamp without time zone DEFAULT now() NOT NULL,
    ipaddr text,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.sms OWNER TO "apiUser";

--
-- Name: sms_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE sms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sms_id_seq OWNER TO "apiUser";

--
-- Name: sms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE sms_id_seq OWNED BY sms.id;


--
-- Name: social; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE social (
    id integer NOT NULL,
    source_id integer NOT NULL,
    provider text NOT NULL,
    uuid text,
    token bytea NOT NULL,
    secret bytea,
    refresh bytea,
    application_id integer,
    sso boolean DEFAULT false NOT NULL,
    ipaddr text,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.social OWNER TO "apiUser";

--
-- Name: social_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE social_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.social_id_seq OWNER TO "apiUser";

--
-- Name: social_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE social_id_seq OWNED BY social.id;


--
-- Name: sources; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE sources (
    id integer NOT NULL,
    user_id integer NOT NULL,
    type text NOT NULL,
    valid boolean DEFAULT false NOT NULL,
    ipaddr text,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.sources OWNER TO "apiUser";

--
-- Name: sources_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE sources_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sources_id_seq OWNER TO "apiUser";

--
-- Name: sources_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE sources_id_seq OWNED BY sources.id;


--
-- Name: spotafriend; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE spotafriend (
    id integer NOT NULL,
    source_id integer NOT NULL,
    provider text NOT NULL,
    target text NOT NULL,
    setup text NOT NULL,
    verified boolean DEFAULT false NOT NULL,
    voided boolean DEFAULT false NOT NULL,
    ipaddr text,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.spotafriend OWNER TO "apiUser";

--
-- Name: spotafriend_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE spotafriend_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.spotafriend_id_seq OWNER TO "apiUser";

--
-- Name: spotafriend_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE spotafriend_id_seq OWNED BY spotafriend.id;


--
-- Name: tasks; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE tasks (
    id integer NOT NULL,
    source_id integer NOT NULL,
    type text NOT NULL,
    running boolean DEFAULT false NOT NULL,
    success boolean DEFAULT false NOT NULL,
    message bytea,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.tasks OWNER TO "apiUser";

--
-- Name: tasks_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE tasks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tasks_id_seq OWNER TO "apiUser";

--
-- Name: tasks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE tasks_id_seq OWNED BY tasks.id;


--
-- Name: user_access; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE user_access (
    id integer NOT NULL,
    identity_id integer NOT NULL,
    user_id integer NOT NULL,
    resource text,
    access boolean DEFAULT true NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL,
    updated timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.user_access OWNER TO "apiUser";

--
-- Name: user_access_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE user_access_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_access_id_seq OWNER TO "apiUser";

--
-- Name: user_access_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE user_access_id_seq OWNED BY user_access.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: apiUser; Tablespace:
--

CREATE TABLE users (
    id integer NOT NULL,
    credential_id integer NOT NULL,
    identity_id integer,
    username bytea NOT NULL,
    created timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.users OWNER TO "apiUser";

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: apiUser
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO "apiUser";

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: apiUser
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY address_lookup ALTER COLUMN id SET DEFAULT nextval('address_lookup_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY applications ALTER COLUMN id SET DEFAULT nextval('applications_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY attributes ALTER COLUMN id SET DEFAULT nextval('attributes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY city_list ALTER COLUMN id SET DEFAULT nextval('city_list_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY companies ALTER COLUMN id SET DEFAULT nextval('companies_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY company_process_handlers ALTER COLUMN id SET DEFAULT nextval('company_process_handlers_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY company_service_handlers ALTER COLUMN id SET DEFAULT nextval('company_service_handlers_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY company_whitelist ALTER COLUMN id SET DEFAULT nextval('company_whitelist_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY controls ALTER COLUMN id SET DEFAULT nextval('controls_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY credential_whitelist ALTER COLUMN id SET DEFAULT nextval('credential_whitelist_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY credentials ALTER COLUMN id SET DEFAULT nextval('credentials_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY digested ALTER COLUMN id SET DEFAULT nextval('digested_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY email ALTER COLUMN id SET DEFAULT nextval('email_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY features ALTER COLUMN id SET DEFAULT nextval('features_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY flags ALTER COLUMN id SET DEFAULT nextval('flags_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY gates ALTER COLUMN id SET DEFAULT nextval('gates_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY hook_errors ALTER COLUMN id SET DEFAULT nextval('hook_errors_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY hooks ALTER COLUMN id SET DEFAULT nextval('hooks_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY identities ALTER COLUMN id SET DEFAULT nextval('identities_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY logs ALTER COLUMN id SET DEFAULT nextval('logs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY metrics ALTER COLUMN id SET DEFAULT nextval('metrics_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY normalised ALTER COLUMN id SET DEFAULT nextval('normalised_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY process_handlers ALTER COLUMN id SET DEFAULT nextval('process_handlers_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY processes ALTER COLUMN id SET DEFAULT nextval('processes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY role_access ALTER COLUMN id SET DEFAULT nextval('role_access_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY scores ALTER COLUMN id SET DEFAULT nextval('scores_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY service_handlers ALTER COLUMN id SET DEFAULT nextval('service_handlers_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY services ALTER COLUMN id SET DEFAULT nextval('services_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY settings ALTER COLUMN id SET DEFAULT nextval('settings_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY sms ALTER COLUMN id SET DEFAULT nextval('sms_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY social ALTER COLUMN id SET DEFAULT nextval('social_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY sources ALTER COLUMN id SET DEFAULT nextval('sources_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY spotafriend ALTER COLUMN id SET DEFAULT nextval('spotafriend_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY tasks ALTER COLUMN id SET DEFAULT nextval('tasks_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY user_access ALTER COLUMN id SET DEFAULT nextval('user_access_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: address_lookup_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY address_lookup
    ADD CONSTRAINT address_lookup_pkey PRIMARY KEY (id);


--
-- Name: applications_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY applications
    ADD CONSTRAINT applications_pkey PRIMARY KEY (id);


--
-- Name: attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY attributes
    ADD CONSTRAINT attributes_pkey PRIMARY KEY (id);


--
-- Name: city_list_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY city_list
    ADD CONSTRAINT city_list_pkey PRIMARY KEY (id);


--
-- Name: companies_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY companies
    ADD CONSTRAINT companies_pkey PRIMARY KEY (id);


--
-- Name: company_process_handlers_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY company_process_handlers
    ADD CONSTRAINT company_process_handlers_pkey PRIMARY KEY (id);


--
-- Name: company_service_handlers_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY company_service_handlers
    ADD CONSTRAINT company_service_handlers_pkey PRIMARY KEY (id);


--
-- Name: company_whitelist_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY company_whitelist
    ADD CONSTRAINT company_whitelist_pkey PRIMARY KEY (id);


--
-- Name: controls_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY controls
    ADD CONSTRAINT controls_pkey PRIMARY KEY (id);


--
-- Name: country_list_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY country_list
    ADD CONSTRAINT country_list_pkey PRIMARY KEY (code);


--
-- Name: credential_whitelist_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY credential_whitelist
    ADD CONSTRAINT credential_whitelist_pkey PRIMARY KEY (id);


--
-- Name: credentials_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY credentials
    ADD CONSTRAINT credentials_pkey PRIMARY KEY (id);


--
-- Name: digested_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY digested
    ADD CONSTRAINT digested_pkey PRIMARY KEY (id);


--
-- Name: email_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY email
    ADD CONSTRAINT email_pkey PRIMARY KEY (id);


--
-- Name: features_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY features
    ADD CONSTRAINT features_pkey PRIMARY KEY (id);


--
-- Name: flags_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY flags
    ADD CONSTRAINT flags_pkey PRIMARY KEY (id);


--
-- Name: gates_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY gates
    ADD CONSTRAINT gates_pkey PRIMARY KEY (id);


--
-- Name: hook_errors_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY hook_errors
    ADD CONSTRAINT hook_errors_pkey PRIMARY KEY (id);


--
-- Name: hooks_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY hooks
    ADD CONSTRAINT hooks_pkey PRIMARY KEY (id);


--
-- Name: identities_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY identities
    ADD CONSTRAINT identities_pkey PRIMARY KEY (id);


--
-- Name: known_name_list_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY known_name_list
    ADD CONSTRAINT known_name_list_pkey PRIMARY KEY (name, type);


--
-- Name: logs_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY logs
    ADD CONSTRAINT logs_pkey PRIMARY KEY (id);


--
-- Name: metrics_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY metrics
    ADD CONSTRAINT metrics_pkey PRIMARY KEY (id);


--
-- Name: name_list_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY name_list
    ADD CONSTRAINT name_list_pkey PRIMARY KEY (country, name);


--
-- Name: normalised_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY normalised
    ADD CONSTRAINT normalised_pkey PRIMARY KEY (id);


--
-- Name: phinxlog_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY phinxlog
    ADD CONSTRAINT phinxlog_pkey PRIMARY KEY (version);


--
-- Name: process_handlers_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY process_handlers
    ADD CONSTRAINT process_handlers_pkey PRIMARY KEY (id);


--
-- Name: processes_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY processes
    ADD CONSTRAINT processes_pkey PRIMARY KEY (id);


--
-- Name: role_access_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY role_access
    ADD CONSTRAINT role_access_pkey PRIMARY KEY (id);


--
-- Name: scores_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY scores
    ADD CONSTRAINT scores_pkey PRIMARY KEY (id);


--
-- Name: service_handlers_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY service_handlers
    ADD CONSTRAINT service_handlers_pkey PRIMARY KEY (id);


--
-- Name: services_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY services
    ADD CONSTRAINT services_pkey PRIMARY KEY (id);


--
-- Name: settings_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (id);


--
-- Name: sms_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY sms
    ADD CONSTRAINT sms_pkey PRIMARY KEY (id);


--
-- Name: social_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY social
    ADD CONSTRAINT social_pkey PRIMARY KEY (id);


--
-- Name: sources_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY sources
    ADD CONSTRAINT sources_pkey PRIMARY KEY (id);


--
-- Name: spotafriend_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY spotafriend
    ADD CONSTRAINT spotafriend_pkey PRIMARY KEY (id);


--
-- Name: tasks_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY tasks
    ADD CONSTRAINT tasks_pkey PRIMARY KEY (id);


--
-- Name: user_access_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY user_access
    ADD CONSTRAINT user_access_pkey PRIMARY KEY (id);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: apiUser; Tablespace:
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: address_lookup_postcode; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX address_lookup_postcode ON address_lookup USING btree (postcode);


--
-- Name: address_lookup_reference; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX address_lookup_reference ON address_lookup USING btree (reference);


--
-- Name: applications_company_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX applications_company_id ON applications USING btree (company_id);


--
-- Name: attributes_identity_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX attributes_identity_id ON attributes USING btree (identity_id);


--
-- Name: city_list_country; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX city_list_country ON city_list USING btree (country);


--
-- Name: city_list_name; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX city_list_name ON city_list USING btree (name);


--
-- Name: city_list_region; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX city_list_region ON city_list USING btree (region);


--
-- Name: companies_public_key; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX companies_public_key ON companies USING btree (public_key);


--
-- Name: company_process_handlers_company_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX company_process_handlers_company_id ON company_process_handlers USING btree (company_id);


--
-- Name: company_service_handlers_company_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX company_service_handlers_company_id ON company_service_handlers USING btree (company_id);


--
-- Name: company_whitelist_company_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX company_whitelist_company_id ON company_whitelist USING btree (company_id);


--
-- Name: company_whitelist_company_id_value; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE UNIQUE INDEX company_whitelist_company_id_value ON company_whitelist USING btree (company_id, value);


--
-- Name: credential_whitelist_credential_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX credential_whitelist_credential_id ON credential_whitelist USING btree (credential_id);


--
-- Name: credential_whitelist_credential_id_value; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE UNIQUE INDEX credential_whitelist_credential_id_value ON credential_whitelist USING btree (credential_id, value);


--
-- Name: credentials_company_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX credentials_company_id ON credentials USING btree (company_id);


--
-- Name: credentials_private; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE UNIQUE INDEX credentials_private ON credentials USING btree (private);


--
-- Name: credentials_public; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE UNIQUE INDEX credentials_public ON credentials USING btree (public);


--
-- Name: digested_name; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX digested_name ON digested USING btree (name);


--
-- Name: digested_source_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX digested_source_id ON digested USING btree (source_id);


--
-- Name: email_email; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX email_email ON email USING btree (email);


--
-- Name: email_source_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX email_source_id ON email USING btree (source_id);


--
-- Name: features_identity_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX features_identity_id ON features USING btree (identity_id);


--
-- Name: features_name; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX features_name ON features USING btree (name);


--
-- Name: flags_identity_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX flags_identity_id ON flags USING btree (identity_id);


--
-- Name: flags_identity_id_name; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE UNIQUE INDEX flags_identity_id_name ON flags USING btree (identity_id, name);


--
-- Name: flags_name; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX flags_name ON flags USING btree (name);


--
-- Name: gates_identity_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX gates_identity_id ON gates USING btree (identity_id);


--
-- Name: gates_identity_id_name; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE UNIQUE INDEX gates_identity_id_name ON gates USING btree (identity_id, name);


--
-- Name: gates_name; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX gates_name ON gates USING btree (name);


--
-- Name: hook_errors_hook_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX hook_errors_hook_id ON hook_errors USING btree (hook_id);


--
-- Name: hooks_credential_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX hooks_credential_id ON hooks USING btree (credential_id);


--
-- Name: identities_public_key; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX identities_public_key ON identities USING btree (public_key);


--
-- Name: known_name_list_dmetaphone1; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX known_name_list_dmetaphone1 ON known_name_list USING btree (dmetaphone1);


--
-- Name: known_name_list_dmetaphone2; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX known_name_list_dmetaphone2 ON known_name_list USING btree (dmetaphone2);


--
-- Name: known_name_list_metaphone; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX known_name_list_metaphone ON known_name_list USING btree (metaphone);


--
-- Name: known_name_list_soundex; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX known_name_list_soundex ON known_name_list USING btree (soundex);


--
-- Name: logs_company_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX logs_company_id ON logs USING btree (company_id);


--
-- Name: logs_credential_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX logs_credential_id ON logs USING btree (credential_id);


--
-- Name: logs_user_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX logs_user_id ON logs USING btree (user_id);


--
-- Name: name_list_dmetaphone1; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX name_list_dmetaphone1 ON name_list USING btree (dmetaphone1);


--
-- Name: name_list_dmetaphone2; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX name_list_dmetaphone2 ON name_list USING btree (dmetaphone2);


--
-- Name: name_list_metaphone; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX name_list_metaphone ON name_list USING btree (metaphone);


--
-- Name: name_list_soundex; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX name_list_soundex ON name_list USING btree (soundex);


--
-- Name: normalised_name; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX normalised_name ON normalised USING btree (name);


--
-- Name: normalised_source_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX normalised_source_id ON normalised USING btree (source_id);


--
-- Name: process_handlers_runlevel; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX process_handlers_runlevel ON process_handlers USING btree (runlevel);


--
-- Name: process_handlers_step; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX process_handlers_step ON process_handlers USING btree (step);


--
-- Name: processes_name; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE UNIQUE INDEX processes_name ON processes USING btree (name);


--
-- Name: role_access_identity_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX role_access_identity_id ON role_access USING btree (identity_id);


--
-- Name: role_access_resource; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX role_access_resource ON role_access USING btree (resource);


--
-- Name: role_access_role; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX role_access_role ON role_access USING btree (role);


--
-- Name: scores_attribute_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX scores_attribute_id ON scores USING btree (attribute_id);


--
-- Name: service_handlers_service_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX service_handlers_service_id ON service_handlers USING btree (service_id);


--
-- Name: service_handlers_source; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX service_handlers_source ON service_handlers USING btree (source);


--
-- Name: services_name; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE UNIQUE INDEX services_name ON services USING btree (name);


--
-- Name: settings_company_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX settings_company_id ON settings USING btree (company_id);


--
-- Name: settings_company_id_category_property; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE UNIQUE INDEX settings_company_id_category_property ON settings USING btree (company_id, category, property);


--
-- Name: sms_phone; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX sms_phone ON sms USING btree (phone);


--
-- Name: sms_source_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX sms_source_id ON sms USING btree (source_id);


--
-- Name: social_application_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX social_application_id ON social USING btree (application_id);


--
-- Name: social_source_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX social_source_id ON social USING btree (source_id);


--
-- Name: sources_user_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX sources_user_id ON sources USING btree (user_id);


--
-- Name: spotafriend_source_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX spotafriend_source_id ON spotafriend USING btree (source_id);


--
-- Name: tasks_source_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX tasks_source_id ON tasks USING btree (source_id);


--
-- Name: user_access_identity_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX user_access_identity_id ON user_access USING btree (identity_id);


--
-- Name: user_access_resource; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX user_access_resource ON user_access USING btree (resource);


--
-- Name: user_access_user_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX user_access_user_id ON user_access USING btree (user_id);


--
-- Name: users_credential_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX users_credential_id ON users USING btree (credential_id);


--
-- Name: users_credential_id_username; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE UNIQUE INDEX users_credential_id_username ON users USING btree (credential_id, username);


--
-- Name: users_identity_id; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX users_identity_id ON users USING btree (identity_id);


--
-- Name: users_username; Type: INDEX; Schema: public; Owner: apiUser; Tablespace:
--

CREATE INDEX users_username ON users USING btree (username);


--
-- Name: applications_company_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY applications
    ADD CONSTRAINT applications_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: attributes_identity_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY attributes
    ADD CONSTRAINT attributes_identity_id FOREIGN KEY (identity_id) REFERENCES identities(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: companies_parent_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY companies
    ADD CONSTRAINT companies_parent_id FOREIGN KEY (parent_id) REFERENCES companies(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: company_process_handlers_company_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY company_process_handlers
    ADD CONSTRAINT company_process_handlers_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: company_process_handlers_handler_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY company_process_handlers
    ADD CONSTRAINT company_process_handlers_handler_id FOREIGN KEY (handler_id) REFERENCES process_handlers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: company_service_handlers_company_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY company_service_handlers
    ADD CONSTRAINT company_service_handlers_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: company_service_handlers_handler_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY company_service_handlers
    ADD CONSTRAINT company_service_handlers_handler_id FOREIGN KEY (handler_id) REFERENCES service_handlers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: company_whitelist_company_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY company_whitelist
    ADD CONSTRAINT company_whitelist_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: credential_whitelist_credential_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY credential_whitelist
    ADD CONSTRAINT credential_whitelist_credential_id FOREIGN KEY (credential_id) REFERENCES credentials(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: credentials_company_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY credentials
    ADD CONSTRAINT credentials_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: digested_source_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY digested
    ADD CONSTRAINT digested_source_id FOREIGN KEY (source_id) REFERENCES sources(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: email_source_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY email
    ADD CONSTRAINT email_source_id FOREIGN KEY (source_id) REFERENCES sources(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: features_identity_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY features
    ADD CONSTRAINT features_identity_id FOREIGN KEY (identity_id) REFERENCES identities(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: flags_identity_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY flags
    ADD CONSTRAINT flags_identity_id FOREIGN KEY (identity_id) REFERENCES identities(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gates_identity_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY gates
    ADD CONSTRAINT gates_identity_id FOREIGN KEY (identity_id) REFERENCES identities(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: hook_errors_hook_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY hook_errors
    ADD CONSTRAINT hook_errors_hook_id FOREIGN KEY (hook_id) REFERENCES hooks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: hooks_credential_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY hooks
    ADD CONSTRAINT hooks_credential_id FOREIGN KEY (credential_id) REFERENCES credentials(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: normalised_source_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY normalised
    ADD CONSTRAINT normalised_source_id FOREIGN KEY (source_id) REFERENCES sources(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: process_handlers_company_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY process_handlers
    ADD CONSTRAINT process_handlers_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: process_handlers_process_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY process_handlers
    ADD CONSTRAINT process_handlers_process_id FOREIGN KEY (process_id) REFERENCES processes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: role_access_identity_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY role_access
    ADD CONSTRAINT role_access_identity_id FOREIGN KEY (identity_id) REFERENCES identities(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: scores_attribute_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY scores
    ADD CONSTRAINT scores_attribute_id FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: service_handlers_company_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY service_handlers
    ADD CONSTRAINT service_handlers_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: service_handlers_service_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY service_handlers
    ADD CONSTRAINT service_handlers_service_id FOREIGN KEY (service_id) REFERENCES services(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: settings_company_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY settings
    ADD CONSTRAINT settings_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: sms_source_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY sms
    ADD CONSTRAINT sms_source_id FOREIGN KEY (source_id) REFERENCES sources(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: social_application_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY social
    ADD CONSTRAINT social_application_id FOREIGN KEY (application_id) REFERENCES applications(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: social_source_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY social
    ADD CONSTRAINT social_source_id FOREIGN KEY (source_id) REFERENCES sources(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: sources_user_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY sources
    ADD CONSTRAINT sources_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: spotafriend_source_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY spotafriend
    ADD CONSTRAINT spotafriend_source_id FOREIGN KEY (source_id) REFERENCES sources(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: tasks_source_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY tasks
    ADD CONSTRAINT tasks_source_id FOREIGN KEY (source_id) REFERENCES sources(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_access_identity_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY user_access
    ADD CONSTRAINT user_access_identity_id FOREIGN KEY (identity_id) REFERENCES identities(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_access_user_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY user_access
    ADD CONSTRAINT user_access_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_credential_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_credential_id FOREIGN KEY (credential_id) REFERENCES credentials(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_identity_id; Type: FK CONSTRAINT; Schema: public; Owner: apiUser
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_identity_id FOREIGN KEY (identity_id) REFERENCES identities(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

