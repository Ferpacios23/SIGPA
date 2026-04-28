/* ============================================================
   SIGPA — Scripts globales (Alpine.js components + utilidades)
   ============================================================ */

/* ── Layout principal ─────────────────────────────────────────────── */
function dashboardApp() {
  return {
    sidebarOpen: false,
    notifOpen:   false,
    toast:       { show: false, msg: '' },

    notifications: [
      { id:1, type:'prestamo', title:'Nueva solicitud de préstamo', body:'Prof. García solicita Aula A101 para mañana 8:00am', time:'Hace 5 min',   color:'var(--blue)',    read:false },
      { id:2, type:'usuario',  title:'Nuevo usuario registrado',    body:'Se registró el usuario Ana López como Secretaría',    time:'Hace 23 min', color:'var(--green)',   read:false },
      { id:3, type:'equipo',   title:'Equipo devuelto',             body:'Laptop EQ-003 fue devuelta por Pedro Suárez',         time:'Hace 1 hora', color:'var(--magenta)', read:true  },
      { id:4, type:'sistema',  title:'Reporte generado',            body:'El reporte mensual de préstamos está listo',           time:'Hace 2 horas',color:'var(--orange)',  read:true  },
    ],

    get unreadCount() { return this.notifications.filter(n => !n.read).length; },

    markSeen()  { setTimeout(() => { this.notifications.forEach(n => n.read = true); }, 2000); },
    clearAll()  { this.notifications = []; this.notifOpen = false; },
    showToast(msg) { this.toast = { show:true, msg }; setTimeout(() => this.toast.show = false, 3500); },

    init() {
      setInterval(() => { /* polling futuro */ }, 60000);
    },
  };
}

/* ── Utilidades de reloj y cuentas regresivas (Secretaría Dashboard) ─ */
function _pad(n) { return String(n).padStart(2, '0'); }

document.addEventListener('DOMContentLoaded', () => {

  // Reloj en tiempo real
  const relojEl = document.getElementById('reloj');
  if (relojEl) {
    setInterval(() => {
      const now = new Date();
      relojEl.textContent = `${_pad(now.getHours())}:${_pad(now.getMinutes())}:${_pad(now.getSeconds())}`;
    }, 1000);
  }

  // Cuentas regresivas
  const countdowns = document.querySelectorAll('[data-segs]');
  if (countdowns.length) {
    setInterval(() => {
      countdowns.forEach(el => {
        let segs = parseInt(el.dataset.segs, 10);
        if (isNaN(segs)) return;
        segs = Math.max(0, segs - 1);
        el.dataset.segs = segs;

        const tipo = el.dataset.tipo;
        const mins = Math.floor(segs / 60);
        const sec  = segs % 60;
        const txt  = el.querySelector('.countdown-txt');
        if (!txt) return;

        if (segs <= 0) {
          txt.textContent = tipo === 'tolerancia' ? 'Liberando...' : 'Finalizando...';
          el.className = 'countdown done';
          return;
        }

        let prefijo = '';
        if (tipo === 'tolerancia')   prefijo = 'Libera en ';
        else if (tipo === 'uso')     prefijo = 'En uso · libera en ';
        else if (tipo === 'horario') prefijo = 'Se desbloquea en ';
        txt.textContent = `${prefijo}${mins}m ${sec}s`;

        if (mins < 10) { el.classList.remove('normal', 'info'); el.classList.add('urgente'); }
      });
    }, 1000);
  }

  // Scroll al momento actual (calendario secretaría)
  const horaAhora = new Date().getHours();
  const horaEl = document.getElementById('hora-' + horaAhora);
  if (horaEl) horaEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
});

/* ── Secretaría Dashboard ─────────────────────────────────────────── */
function secretariaApp() {
  return {
    showCreate: false,
    showCancel: false,
    cancelId:   null,
    openCreate()   { this.showCreate = true; },
    openCancel(id) { this.cancelId = id; this.showCancel = true; },
  };
}

/* ── Secretaría — Préstamos de aulas ─────────────────────────────── */
function prestamosApp() {
  const cfg = window.SIGPA_PAGE || {};
  return {
    showCreate:                false,
    showCancel:                false,
    showAsignarEquipo:         false,
    cancelId:                  null,
    asignarPrestamoId:         null,
    prestamosPendientesCount:  cfg.pendientesCount ?? 0,

    openCancel(id)        { this.cancelId = id; this.showCancel = true; },
    openAsignarEquipo(id) { this.asignarPrestamoId = id; this.showAsignarEquipo = true; },
  };
}

/* ── Secretaría — Préstamos de equipos ───────────────────────────── */
function equiposSecretariaApp() {
  const cfg = window.SIGPA_PAGE || {};
  return {
    showCreate:   cfg.hasErrors ?? false,
    showCancel:   false,
    showDevolver: false,
    cancelId:     null,
    devolverIdVal: null,

    openCancel(id)   { this.cancelId = id; this.showCancel = true; },
    openDevolver(id) { this.devolverIdVal = id; this.showDevolver = true; },
  };
}

/* ── Técnico TI — Dashboard ───────────────────────────────────────── */
function tecnicoApp() {
  return {
    showAsignar:    false,
    showDevolver:   false,
    asignarId:      null,
    asignarAula:    '',
    devolverIdVal:  null,
    devolverNombre: '',

    openAsignar(id, aula)    { this.asignarId = id; this.asignarAula = aula; this.showAsignar = true; },
    openDevolver(id, nombre) { this.devolverIdVal = id; this.devolverNombre = nombre; this.showDevolver = true; },
  };
}

/* ── Técnico TI — Asignaciones ────────────────────────────────────── */
function asignacionesApp() {
  return {
    showAsignar:    false,
    showDevolver:   false,
    asignarId:      null,
    asignarAula:    '',
    devolverIdVal:  null,
    devolverNombre: '',

    openAsignar(id, aula)    { this.asignarId = id; this.asignarAula = aula; this.showAsignar = true; },
    openDevolver(id, nombre) { this.devolverIdVal = id; this.devolverNombre = nombre; this.showDevolver = true; },
  };
}

/* ── Técnico TI — Inventario ──────────────────────────────────────── */
function inventarioApp() {
  const cfg = window.SIGPA_PAGE || {};
  return {
    showRegistrar: cfg.hasErrors ?? false,
    showEstado:    false,
    estadoData:    {},
    obsText:       '',
    obsLen:        0,
    showVer:       false,
    verData:       {},

    openEstado(data) { this.estadoData = data; this.obsText = ''; this.obsLen = 0; this.showEstado = true; },
    openVer(data)    { this.verData = data; this.showVer = true; },
  };
}

/* ── Admin — Aulas ────────────────────────────────────────────────── */
function aulasApp() {
  const cfg = window.SIGPA_PAGE || {};
  return {
    showModal:    false,
    showDelete:   false,
    editId:       null,
    deleteId:     null,
    deleteCode:   '',
    search:       '',
    filterEstado: '',
    viewMode:     'grid',
    form: { codigo:'', nombre:'', capacidad:'', ubicacion:'', descripcion:'', estado:'disponible', activa:true },
    aulasData: cfg.aulasData ?? {},

    init() {},

    matchFilter(text, estado) {
      const q = this.search.toLowerCase();
      return (!q || text.includes(q)) && (!this.filterEstado || estado === this.filterEstado);
    },

    openCreate() {
      this.editId = null;
      this.form = { codigo:'', nombre:'', capacidad:'', ubicacion:'', descripcion:'', estado:'disponible', activa:true };
      this.showModal = true;
    },

    openEdit(id) {
      const a = this.aulasData[id];
      if (!a) return;
      this.editId = id;
      this.form = {
        codigo:      a.codigo,
        nombre:      a.nombre ?? '',
        capacidad:   a.capacidad,
        ubicacion:   a.ubicacion ?? '',
        descripcion: a.descripcion ?? '',
        estado:      a.estado,
        activa:      !!a.activa,
      };
      this.showModal = true;
    },

    confirmDelete(id, code) { this.deleteId = id; this.deleteCode = code; this.showDelete = true; },
  };
}

/* ── Admin — Usuarios ─────────────────────────────────────────────── */
function usuariosApp() {
  const cfg = window.SIGPA_PAGE || {};
  return {
    showModal: false,
    editId:    null,
    search:    '',
    usersData: cfg.usersData ?? {},
    form: { name:'', email:'', password:'', role_id:'', identificacion:'', telefono:'', dependencia:'', activo:true },

    openCreate() {
      this.editId = null;
      this.form = { name:'', email:'', password:'', role_id:'', identificacion:'', telefono:'', dependencia:'', activo:true };
      this.showModal = true;
    },

    openEdit(id) {
      const u = this.usersData[id];
      if (!u) return;
      this.editId = id;
      this.form = {
        name:           u.name,
        email:          u.email,
        password:       '',
        role_id:        u.profile?.role_id ?? '',
        identificacion: u.profile?.identificacion ?? '',
        telefono:       u.profile?.telefono ?? '',
        dependencia:    u.profile?.dependencia ?? '',
        activo:         u.profile?.activo ?? true,
      };
      this.showModal = true;
    },
  };
}

/* ── Admin — Docentes ─────────────────────────────────────────────── */
function docentesApp() {
  const cfg = window.SIGPA_PAGE || {};
  return {
    showCreate: cfg.hasErrors ?? false,
    showEdit:   false,
    editData:   {},
    openEdit(data) { this.editData = data; this.showEdit = true; },
  };
}

/* ── Admin — Equipos ──────────────────────────────────────────────── */
function equiposAdminApp() {
  const cfg = window.SIGPA_PAGE || {};
  return {
    showModal:    false,
    editId:       null,
    search:       '',
    filterDisp:   '',
    filterEstado: '',
    codigoError:  '',
    codigoOk:     false,
    equiposData:  cfg.equiposData ?? {},
    form: { nombre:'', codigo_inventario:'', estado_fisico:'bueno', marca:'', modelo:'', ubicacion_almacenamiento:'', fecha_adquisicion:'', disponible:true, activo:true, descripcion:'' },

    init() {
      if (cfg.hasErrors) {
        this.showModal   = true;
        this.editId      = cfg.editId ?? null;
        this.form        = cfg.oldForm ?? this.form;
        this.codigoError = cfg.codigoError ?? '';
      }
    },

    matchFilter(text, disp, estado) {
      const matchQ = !this.search      || text.includes(this.search.toLowerCase());
      const matchD = !this.filterDisp  || disp   === this.filterDisp;
      const matchE = !this.filterEstado|| estado  === this.filterEstado;
      return matchQ && matchD && matchE;
    },

    openCreate() {
      this.editId = null;
      this.codigoError = ''; this.codigoOk = false;
      this.form = { nombre:'', codigo_inventario:'', estado_fisico:'bueno', marca:'', modelo:'', ubicacion_almacenamiento:'', fecha_adquisicion:'', disponible:true, activo:true, descripcion:'' };
      this.showModal = true;
    },

    openEdit(id) {
      const e = this.equiposData[id];
      if (!e) return;
      this.editId = id;
      this.codigoError = ''; this.codigoOk = false;
      this.form = {
        nombre:                    e.nombre,
        codigo_inventario:         e.codigo_inventario,
        estado_fisico:             e.estado_fisico,
        marca:                     e.marca ?? '',
        modelo:                    e.modelo ?? '',
        ubicacion_almacenamiento:  e.ubicacion_almacenamiento ?? '',
        fecha_adquisicion:         e.fecha_adquisicion ?? '',
        disponible:                !!e.disponible,
        activo:                    !!e.activo,
        descripcion:               e.descripcion ?? '',
      };
      this.showModal = true;
    },

    async checkCodigo() {
      const codigo = this.form.codigo_inventario.trim();
      if (!codigo) { this.codigoError = ''; this.codigoOk = false; return; }

      const params = new URLSearchParams({ codigo });
      if (this.editId) params.append('exclude', this.editId);

      const res  = await fetch((cfg.checkCodigoUrl ?? '') + '?' + params, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });
      const data = await res.json();

      if (data.exists) { this.codigoError = 'Este código de inventario ya está registrado.'; this.codigoOk = false; }
      else             { this.codigoError = '';  this.codigoOk = true; }
    },
  };
}

/* ── Admin — Horarios ─────────────────────────────────────────────── */
function horariosApp() {
  const cfg = window.SIGPA_PAGE || {};
  const horariosData = cfg.horariosData ?? {};
  const emptyForm = {
    docente_id:'', aula_id:'', materia:'', grupo:'',
    dia_semana:'', hora_inicio:'', hora_fin:'',
    fecha_inicio:'', fecha_fin:'', activo:true,
  };

  return {
    showModal: false,
    editId:    null,
    form:      { ...emptyForm },

    init() {
      if (cfg.hasErrors) {
        this.showModal = true;
        this.editId    = cfg.editId ?? null;
        this.form      = cfg.oldForm ? { ...emptyForm, ...cfg.oldForm, activo:true } : { ...emptyForm };
      }
    },

    openCreate() { this.editId = null; this.form = { ...emptyForm }; this.showModal = true; },

    openEdit(id) {
      const h = horariosData[id];
      if (!h) return;
      this.editId = id;
      this.form = {
        docente_id:   String(h.docente_id),
        aula_id:      String(h.aula_id),
        materia:      h.materia,
        grupo:        h.grupo ?? '',
        dia_semana:   h.dia_semana,
        hora_inicio:  h.hora_inicio,
        hora_fin:     h.hora_fin,
        fecha_inicio: h.fecha_inicio,
        fecha_fin:    h.fecha_fin,
        activo:       !!h.activo,
      };
      this.showModal = true;
    },
  };
}

/* ── Admin — Horarios de aula ─────────────────────────────────────── */
function horariosAulaApp() {
  const cfg = window.SIGPA_PAGE || {};
  return {
    showForm:   cfg.hasErrors ?? false,
    dia:        '',
    horaInicio: '',
    horaFin:    '',
    get conflicto() {
      if (!this.dia || !this.horaInicio || !this.horaFin) return null;
      if (this.horaFin <= this.horaInicio) return null;
      const horarios = cfg.horarios || [];
      const match = horarios.find(h =>
        h.dia === this.dia &&
        this.horaInicio < h.fin &&
        this.horaFin    > h.inicio
      );
      return match
        ? `Esa hora ya está separada: "${match.materia}" ocupa de ${match.inicio} a ${match.fin} el día seleccionado.`
        : null;
    },
  };
}

/* ── Admin — Dashboard ────────────────────────────────────────────── */
function adminDash() {
  return {};
}

/* ── Secretaría — Vista de aulas (búsqueda y filtrado) ───────────── */
function aulasSecretariaApp() {
  return {
    search:       '',
    filterEstado: '',
  };
}

/* ── Secretaría — Tarjeta de cancelación durante clase ───────────── */
/*
 * horaInicio / horaFin: strings "HH:MM" del préstamo activo.
 * Mantiene un timer que actualiza `now` cada 15 s para que las
 * propiedades reactivas recalculen sin recargar la página.
 *
 * Estados:
 *   enCurso && !habilitado  → botón deshabilitado, muestra cuenta regresiva
 *   enCurso &&  habilitado  → botón rojo activo "Cancelar clase y liberar"
 *  !enCurso                 → botón estándar "Finalizar y liberar"
 */
function cancelacionClaseCard(horaInicio, horaFin) {
  return {
    now: new Date(),
    _timer: null,

    get _inicio() {
      const [h, m] = horaInicio.split(':');
      const d = new Date(); d.setHours(+h, +m, 0, 0); return d;
    },
    get _fin() {
      const [h, m] = horaFin.split(':');
      const d = new Date(); d.setHours(+h, +m, 0, 0); return d;
    },
    get _habilitadoEn() { return new Date(this._inicio.getTime() + 30 * 60_000); },

    get enCurso()          { return this.now >= this._inicio && this.now < this._fin; },
    get habilitado()       { return this.now >= this._habilitadoEn; },
    get minutosRestantes() {
      return Math.max(1, Math.ceil((this._habilitadoEn - this.now) / 60_000));
    },

    init()    { this._timer = setInterval(() => { this.now = new Date(); }, 15_000); },
    destroy() { clearInterval(this._timer); },
  };
}
