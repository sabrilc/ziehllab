// BuscadorCliente.jsx actualizado para manejar ID internamente
const BuscadorCliente = ({
                             clienteSeleccionado,
                             setClienteSeleccionado,
                             clienteNuevo,
                             setClienteNuevo,
                             bloqueado = false, // nueva prop
                         }) => {
    const [query, setQuery] = React.useState('');
    const [resultados, setResultados] = React.useState([]);
    const [cargando, setCargando] = React.useState(false);
    const [error, setError] = React.useState(null);
    const [mostrarFormulario, setMostrarFormulario] = React.useState(false);

    const buscarClientes = (q) => {
        setQuery(q);
        if (q.length < 3) {
            setResultados([]);
            return;
        }
        setCargando(true);
        axios.get('/lab/orden/cliente-buscar', { params: { q } })
            .then(res => {
                setResultados(res.data);
                setError(null);
            })
            .catch(() => {
                setResultados([]);
                setError('Error buscando clientes');
            })
            .finally(() => setCargando(false));
    };

    const seleccionarCliente = (cliente) => {
        if (bloqueado) return;
        setClienteSeleccionado(cliente);
        setClienteNuevo({
            id: cliente.id?? '',
            email: cliente.email ?? '',
            email_notificacion: cliente.email_notificacion ?? '',
            identificacion: cliente.identificacion?? '',
            nombres: cliente.nombres?? '',
            sexo_id: cliente.sexo_id?? '',
            telefono: cliente.telefono?? '',
            direccion: cliente.direccion?? '',
            fecha_nacimiento: cliente.fecha_nacimiento?? '',
        });
        setResultados([]);
        setQuery('');
        setMostrarFormulario(false);
    };

    const iniciarNuevoCliente = () => {
        if (bloqueado) return;
        setClienteSeleccionado(null);
        setClienteNuevo({
            id: null,
            email: '',
            email_notificacion: '',
            identificacion: '',
            nombres: '',
            sexo_id: '',
            telefono: '',
            direccion: '',
            fecha_nacimiento: '',
        });
        setResultados([]);
        setMostrarFormulario(true);
    };

    const guardarCliente = () => {
        const formData = new FormData();
        Object.entries(clienteNuevo).forEach(([key, value]) => {
            console.log({key, value})
            formData.append(key, value ?? '');
        });

        axios.post('/lab/orden/cliente-guardar', formData)
            .then(res => {
                if (res.data && res.data.success) {
                    swal('Info', 'Cliente guardado correctamente', 'success');
                    seleccionarCliente(res.data.cliente);
                } else {
                    swal('Advertencia', 'Error al guardar el cliente', 'warning');
                }
            })
            .catch(() => alert('Error de conexión al guardar cliente'));
    };

    return (
        <div className="panel panel-default">
            <div className="panel-heading">👤 Buscar Cliente</div>
            <div className="panel-body">

                {!clienteSeleccionado && !mostrarFormulario && !bloqueado && (
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Buscar por nombre o identificación..."
                        value={query}
                        onChange={(e) => buscarClientes(e.target.value)}
                    />
                )}

                {!bloqueado && cargando && <div style={{ marginTop: '5px' }}>Buscando...</div>}
                {!bloqueado && error && <div className="badge badge-info">{error}</div>}

                {!bloqueado && resultados.length > 0 && (
                    <ul className="list-group" style={{ marginTop: '5px', maxHeight: '150px', overflowY: 'auto' }}>
                        {resultados.map(cliente => (
                            <li
                                key={cliente.id}
                                className="list-group-item"
                                style={{ cursor: 'pointer' }}
                                onClick={() => seleccionarCliente(cliente)}
                            >
                                {cliente.nombres} ({cliente.identificacion})
                            </li>
                        ))}
                    </ul>
                )}

                {!bloqueado && query.length >= 3 && resultados.length === 0 && !cargando && (
                    <div className="badge badge-info" style={{ marginTop: '5px' }}>
                        No se encontraron clientes.
                        <button className="btn btn-primary btn-sm" onClick={iniciarNuevoCliente} style={{ marginLeft: '10px' }}>
                           <i className={"fa fa-plus-circle"}></i> Registrar nuevo cliente
                        </button>
                    </div>
                )}

                {clienteSeleccionado && !mostrarFormulario && (
                    <div className="alert alert-success" style={{ marginTop: '10px' }}>
                        <strong>Cliente seleccionado:</strong> {clienteSeleccionado.nombres} ({clienteSeleccionado.identificacion})
                        {!bloqueado && (
                            <div className="text-right" style={{ marginTop: '10px' }}>
                                <button
                                    className="btn btn-primary btn-sm"
                                    onClick={() => setMostrarFormulario(true)}
                                >
                                    <i className={"fa fa-edit"}></i> Editar cliente
                                </button>
                                {' '}
                                <button
                                    className="btn btn-danger btn-sm"
                                    onClick={() => {
                                        setClienteSeleccionado(null);
                                        setClienteNuevo({
                                            id: null,
                                            email: '',
                                            email_notificacion: '',
                                            identificacion: '',
                                            nombres: '',
                                            sexo_id: '',
                                            telefono: '',
                                            direccion: '',
                                            fecha_nacimiento: '',
                                        });
                                        setQuery('');
                                        setResultados([]);
                                    }}
                                >
                                    <i className={"fa fa-remove"}></i> Quitar cliente
                                </button>
                            </div>
                        )}
                    </div>
                )}

                {!bloqueado && mostrarFormulario && (
                    <div className="row" style={{ marginTop: '10px' }}>
                        <div className="col-md-6">
                            <div className="form-group">
                                <label>Nombres</label>
                                <input
                                    className="form-control"
                                    value={clienteNuevo.nombres}
                                    onChange={e => setClienteNuevo({ ...clienteNuevo, nombres: e.target.value })}
                                />
                            </div>
                            <div className="form-group">
                                <label>Identificación</label>
                                <input
                                    className="form-control"
                                    value={clienteNuevo.identificacion}
                                    onChange={e => setClienteNuevo({ ...clienteNuevo, identificacion: e.target.value })}
                                />
                            </div>
                            <div className="form-group">
                                <label>Correo electrónico</label>
                                <input
                                    type="email"
                                    className="form-control"
                                    value={clienteNuevo.email}
                                    onChange={e => setClienteNuevo({ ...clienteNuevo, email: e.target.value })}
                                />
                            </div>
                            <div className="form-group">
                                <label>Correo notificación</label>
                                <input
                                    type="email"
                                    className="form-control"
                                    value={clienteNuevo.email_notificacion}
                                    onChange={e => setClienteNuevo({ ...clienteNuevo, email_notificacion: e.target.value })}
                                />
                            </div>
                        </div>

                        <div className="col-md-6">
                            <div className="form-group">
                                <label>Teléfono</label>
                                <input
                                    className="form-control"
                                    value={clienteNuevo.telefono}
                                    onChange={e => setClienteNuevo({ ...clienteNuevo, telefono: e.target.value })}
                                />
                            </div>
                            <div className="form-group">
                                <label>Dirección</label>
                                <input
                                    className="form-control"
                                    value={clienteNuevo.direccion}
                                    onChange={e => setClienteNuevo({ ...clienteNuevo, direccion: e.target.value })}
                                />
                            </div>
                            <div className="form-group">
                                <label>Fecha de nacimiento</label>
                                <input
                                    type="date"
                                    className="form-control"
                                    value={clienteNuevo.fecha_nacimiento}
                                    onChange={e => setClienteNuevo({ ...clienteNuevo, fecha_nacimiento: e.target.value })}
                                />
                            </div>
                            <div className="form-group">
                                <label>Sexo</label>
                                <select
                                    className="form-control"
                                    value={clienteNuevo.sexo_id}
                                    onChange={e => setClienteNuevo({ ...clienteNuevo, sexo_id: e.target.value })}
                                >
                                    <option value="">-- Seleccione --</option>
                                    <option value="1">Masculino</option>
                                    <option value="2">Femenino</option>
                                    <option value="3">Otro</option>
                                </select>
                            </div>
                        </div>

                        <div className="col-md-12 text-right">
                            <button className="btn btn-success" onClick={guardarCliente}>
                                <i className={"fa fa-save"}></i> Guardar cliente
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};


// Componente para buscar productos y seleccionar
const BuscadorProducto = ({ onProductoSeleccionado }) => {
    const [query, setQuery] = React.useState('');
    const [resultados, setResultados] = React.useState([]);
    const [cargando, setCargando] = React.useState(false);
    const [error, setError] = React.useState(null);

    const buscarProductos = (q) => {
        setQuery(q);
        if (q.length < 2) {
            setResultados([]);
            return;
        }
        setCargando(true);
        axios
            .get('/lab/orden/analisis-buscar', { params: { q } })
            .then((res) => {
                setResultados(res.data);
                setError(null);
            })
            .catch(() => {
                setError('Error buscando productos');
                setResultados([]);
            })
            .finally(() => setCargando(false));
    };

    return (
        <div className="panel panel-default" style={{ marginBottom: '15px' }}>
            <div className="panel-heading">🔍 Buscar Producto</div>
            <div className="panel-body">
                <input
                    type="text"
                    className="form-control"
                    placeholder="Escribe para buscar producto..."
                    value={query}
                    onChange={(e) => buscarProductos(e.target.value)}
                />
                {cargando && <div style={{ marginTop: '5px' }}>Buscando...</div>}
                {error && <div className="alert alert-danger" style={{ marginTop: '5px' }}>{error}</div>}

                {resultados.length > 0 && (
                    <ul
                        className="list-group"
                        style={{ maxHeight: '150px', overflowY: 'auto', marginTop: '5px' }}
                    >
                        {resultados.map((prod) => (
                            <li
                                key={prod.id}
                                className="list-group-item list-group-item-action"
                                style={{ cursor: 'pointer' }}
                                onClick={() => {
                                    onProductoSeleccionado(prod);
                                    setQuery('');
                                    setResultados([]);
                                }}
                            >
                                {prod.descripcion} - ${prod.precio}
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </div>
    );
};

// Fila individual de producto en la lista
const ItemRow = ({ index, item,editable, actualizarItem, eliminarItem }) => {
    const bloqueado = !!item.idFacturaDetalle; // Si ya guardado, bloquea edición

    return (
        <tr>
            <td>
                <input
                    type="text"
                    className="form-control"
                    value={item.descripcion}
                    disabled={true}
                    onChange={(e) => actualizarItem(index, 'descripcion', e.target.value)}
                />
            </td>
            <td>
                <input
                    type="number"
                    className="form-control"
                    min="1"
                    value={item.cantidad}
                    disabled={true}
                    onChange={(e) => actualizarItem(index, 'cantidad', e.target.value)}
                />
            </td>
            <td>
                <input
                    type="number"
                    className="form-control"
                    min="0"
                    step="0.01"
                    value={item.precio}
                    disabled={true}
                    onChange={(e) => actualizarItem(index, 'precio', e.target.value)}
                />
            </td>
            <td>${(item.cantidad * item.precio).toFixed(2)}</td>
            <td>{ editable &&
                <button
                    className="btn btn-danger btn-sm"
                    onClick={() => eliminarItem(index)}
                    title="Eliminar producto"
                >
                    X
                </button>
            }
            </td>
        </tr>
    );
};

// Lista de productos agregados
const ListaItems = ({ items, editable, actualizarItem, eliminarItem }) => {
    return (
        <>
            <table className="table table-bordered table-condensed">
                <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Total</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {items.length === 0 && (
                    <tr>
                        <td colSpan="5" style={{ textAlign: 'center' }}>
                            No hay productos agregados
                        </td>
                    </tr>
                )}
                {items.map((item, idx) => (
                    <ItemRow
                        key={idx}
                        index={idx}
                        item={item}
                        editable={editable}
                        actualizarItem={actualizarItem}
                        eliminarItem={eliminarItem}
                    />
                ))}
                </tbody>
            </table>

        </>
    );
};

// Componente principal OrdenApp
const OrdenApp = () => {
    const [clientes, setClientes] = React.useState([]);
    const [clienteSeleccionado, setClienteSeleccionado] = React.useState(null);
    const [clienteNuevo, setClienteNuevo] = React.useState({ nombre: '', cedula: '', nacimiento: '' });

    const [items, setItems] = React.useState([]);
    const [descuento, setDescuento] = React.useState(0);
    const [guardado, setGuardado] = React.useState(false);
    const [ordenId, setOrdenId] = React.useState(0)
    const [total, setTotal] = React.useState(0);


    React.useEffect(() => {
        const subtotal = items.reduce((sum, item) => sum + item.cantidad * item.precio, 0);
        const desc = descuento > 0 ? (subtotal * descuento) / 100 : 0;
        setTotal(subtotal - desc);
    }, [items, descuento]);

    const seleccionarCliente = (e) => {
        const id = e.target.value;
        if (id === 'nuevo') {
            setClienteSeleccionado(null);
            setClienteNuevo({ nombre: '', cedula: '', nacimiento: '' });
        } else {
            const cliente = clientes.find((c) => c.id === parseInt(id));
            setClienteSeleccionado(cliente || null);
            setClienteNuevo(cliente ? { nombre: cliente.nombre, cedula: cliente.cedula, nacimiento: cliente.nacimiento } : { nombre: '', cedula: '', nacimiento: '' });
        }
    };

    const actualizarClienteNuevo = (campo, valor) => {
        setClienteNuevo({ ...clienteNuevo, [campo]: valor });
    };

    const agregarProducto = (producto) => {
        const existe = items.find((i) => i.idProducto === producto.id);
        if (existe) {
            alert('Producto ya agregado');
            return;
        }
        const nuevoItem = {
            idProducto: producto.id,
            descripcion: producto.descripcion,
            cantidad: 1,
            precio: producto.precio,
        };
        setItems([...items, nuevoItem]);
    };

     const actualizarItem = (index, campo, valor) => {
        const nuevos = [...items];
        nuevos[index][campo] = campo === 'descripcion' ? valor : parseFloat(valor);
        setItems(nuevos);
    };

    const eliminarItem = (index) => {
        const nuevos = [...items];
        nuevos.splice(index, 1);
        setItems(nuevos);
    };

    const guardarOrden = () => {
        const formData = new FormData();
        const cliente = clienteSeleccionado ? clienteSeleccionado : clienteNuevo;
        formData.append('cliente', JSON.stringify(cliente));
        formData.append('items', JSON.stringify(items));
        formData.append('descuento', descuento);
        formData.append('total', total);

        axios
            .post('/lab/orden/guardar', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then((res) => {
                let data = res.data
                if(data.success=== true){
                    swal("Transacción realizada con Exito!.", " 'Orden guardada correctamente", "success");
                    setGuardado(true);
                    setOrdenId(data.orden_id)
                }else{
                    swal("Transacción fallida", data.error, "error");
                }

            })
            .catch((err) => {
                swal("Transacción fallida", err, "error");
                alert('Error guardando orden');
            });
    };

    const print = () => {
        const formData = new FormData();
        formData.append('ticket', ordenId);

        axios
            .post('/lab/orden/print-ticket', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then((res) => {
                const data = res.data;

                if (data.success === true && data.pdf) {
                    // Convertir base64 a Blob
                    const byteCharacters = atob(data.pdf);
                    const byteNumbers = new Array(byteCharacters.length);
                    for (let i = 0; i < byteCharacters.length; i++) {
                        byteNumbers[i] = byteCharacters.charCodeAt(i);
                    }
                    const byteArray = new Uint8Array(byteNumbers);
                    const pdfBlob = new Blob([byteArray], { type: 'application/pdf' });

                    // Crear URL para imprimir
                    const pdfUrl = URL.createObjectURL(pdfBlob);

                    // Abrir en nueva ventana y lanzar impresión
                    const printWindow = window.open(pdfUrl);
                    if (printWindow) {
                        printWindow.onload = () => {
                            printWindow.focus();
                            printWindow.print();
                        };
                    } else {
                        swal("Error", "No se pudo abrir la ventana de impresión.", "error");
                    }

                } else {
                    swal("Transacción fallida", data.error || "Error desconocido", "error");
                }

            })
            .catch((err) => {
                swal("Transacción fallida", err?.message || "Error inesperado", "error");
            });
    };


    const nuevaOrden = () => {
        setClienteSeleccionado(null);
        setClienteNuevo({ nombre: '', cedula: '', nacimiento: '' });
        setItems([]);
        setDescuento(0);
        setGuardado(false);
        setTotal(0);
    };

    return (
        <div className="container">
            <h3>📝 Crear Orden</h3>

            <BuscadorCliente
                clienteSeleccionado={clienteSeleccionado}
                setClienteSeleccionado={setClienteSeleccionado}
                clienteNuevo={clienteNuevo}
                setClienteNuevo={setClienteNuevo}
                bloqueado={guardado}
            />


            {!guardado && <BuscadorProducto onProductoSeleccionado={agregarProducto} />}

            <ListaItems
                items={items}
                editable={!guardado}
                actualizarItem={actualizarItem}
                eliminarItem={eliminarItem}
            />

            <div className="form-group" style={{ marginTop: 10, maxWidth: 200 }}>
                <label>Descuento (%)</label>
                <input
                    type="number"
                    className="form-control"
                    value={descuento}
                    min="0"
                    max="100"
                    onChange={(e) => setDescuento(parseFloat(e.target.value) || 0)}
                />
            </div>

            <div className="alert alert-info">
                <strong>Total: </strong> ${total.toFixed(2)}
            </div>

            {!guardado ? (
                <button className="btn btn-success" onClick={guardarOrden}>
                    <i className={"fa fa-save"}></i> Guardar Orden
                </button>
            ) : (
                <div className="btn-group">
                    <button className="btn btn-default" onClick={nuevaOrden}>
                        <i className={"fa fa-plus"}></i> Nueva Orden
                    </button>
                    <button className="btn btn-info" onClick={() => print()}>
                        <i className={"fa fa-print"}></i> Imprimir
                    </button>
                </div>
            )}
        </div>
    );
};

// Renderizamos en DOM
    const root = ReactDOM.createRoot(document.getElementById('react-root'));
    root.render(<OrdenApp />);

