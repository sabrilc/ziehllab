const PdfModal = ({ ordenId }) => {
    const [show, setShow] = React.useState(false);
    const [pdfUrl, setPdfUrl] = React.useState('');
	const [isLoading, setIsLoading] = React.useState(false);
    const modalRef = React.useRef(null);
    const iframeRef = React.useRef(null);


    const openModal = () => {
        const url = `/lab/orden/online-orden?id=${ordenId}`;
        setPdfUrl(url);
		 setIsLoading(true); 
        setShow(true);
    };

    const closeModal = () => {
        setShow(false);
		setIsLoading(false);
        setPdfUrl('');
    };

    // Limpiar iframe cuando se cierra con Bootstrap manualmente
    React.useEffect(() => {
        const $modal = window.$(modalRef.current);
        $modal.on('hidden.bs.modal', () => {
            setPdfUrl('');
			setIsLoading(false);
        });

        return () => {
            $modal.off('hidden.bs.modal');
        };
    }, []);

    React.useEffect(() => {
        if (show) {
            window.$(modalRef.current).modal('show');
        } else {
            window.$(modalRef.current).modal('hide');
        }
    }, [show]);

    // Lanzar impresi贸n cuando el PDF termine de cargar en el iframe
    const onIframeLoad = () => {
		 setIsLoading(false);
        const iframe = iframeRef.current;
        if (iframe && iframe.contentWindow) {
            try {
                iframe.contentWindow.focus();
               // iframe.contentWindow.print();
            } catch (e) {
                console.error("Error al lanzar impresi贸n desde el iframe:", e);
            }
        }
    };

    return (
        <>
            <button className="btn btn-primary" onClick={openModal}>
               <i className={"fa fa-print"} ></i>Imprimir
            </button>

            <div
                ref={modalRef}
                className="modal fade"
                tabIndex="-1"
                role="dialog"
                aria-hidden="true"
                id="modal-pdf"
            >
                <div className="modal-dialog" style={{ width: '90%', maxWidth: 900 }}>
                    <div className="modal-content">
                        <div className="modal-header">
                            <button
                                type="button"
                                className="close"
                                onClick={closeModal}
                                aria-label="Cerrar"
                            >
                                &times;
                            </button>
                            <h4 className="modal-title">Vista de Impresion</h4>
                        </div>
                        <div className="modal-body" style={{ height: '80vh', padding: 0 }}>
						{/* Loader SVG */}
                            {isLoading && (
                                <div
                                    style={{
                                        position: 'absolute',
                                        top: '50%',
                                        left: '50%',
                                        transform: 'translate(-50%, -50%)',
                                        zIndex: 10,
                                        background: 'rgba(255,255,255,0.7)',
                                        padding: '20px',
                                        borderRadius: '8px'
                                    }}
                                >
                                    <img src="/imagen/loading.svg" alt="Cargando PDF..." />
                                </div>
                            )}
						
                            {pdfUrl && (
                                <iframe
                                    src={pdfUrl}
                                    ref={iframeRef}
                                    width="100%"
                                    height="100%"
                                    frameBorder="0"
                                    onLoad={onIframeLoad}
                                    title="Vista previa PDF"
                                />
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};


