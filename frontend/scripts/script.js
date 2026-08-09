const API_URL = "http://127.0.0.1:1337";

let productos = [];
let productoEditando = null;


/*
|--------------------------------------------------------------------------
| DOM
|--------------------------------------------------------------------------
*/

const tabla = document.querySelector("tbody");
const buscador = document.querySelector(".table-search input");
const botonAgregar = document.querySelector(".primary-button");


/*
|--------------------------------------------------------------------------
| CARGAR PRODUCTOS
|--------------------------------------------------------------------------
*/

async function cargarProductos() {

    try {

        const response = await fetch(`${API_URL}/productos`);

        const result = await response.json();

        if (!response.ok || !result.success) {

            throw new Error(
                result.message || "No se pudieron obtener los productos"
            );
        }

        productos = result.data;

        renderizarProductos(productos);
        actualizarDashboard(productos);
        renderizarAlertas(productos);

    } catch (error) {

        console.error("Error cargando productos:", error);

        mostrarNotificacion(
            "No se pudo conectar con el servidor",
            "error"
        );
    }
}


/*
|--------------------------------------------------------------------------
| RENDER PRODUCTOS
|--------------------------------------------------------------------------
*/

function renderizarProductos(lista) {

    tabla.innerHTML = "";

    if (lista.length === 0) {

        tabla.innerHTML = `
            <tr>
                <td colspan="7" class="empty-table">
                    No se encontraron productos
                </td>
            </tr>
        `;

        return;
    }


    lista.forEach(producto => {

        const tr = document.createElement("tr");

        const stockClass =
            producto.unidades <= 5
                ? "low"
                : producto.unidades <= 10
                    ? "medium"
                    : "high";


        const estado = producto.estado
            ? `
                <span class="status active-status">
                    Activo
                </span>
            `
            : `
                <span class="status danger-status">
                    Inactivo
                </span>
            `;


        tr.innerHTML = `

            <td>

                <div class="product-cell">

                    <div class="product-image ${obtenerColorProducto(producto.id)}">
                        ${obtenerIniciales(producto.nombre)}
                    </div>

                    <div>

                        <strong>
                            ${escaparHTML(producto.nombre)}
                        </strong>

                        <span>
                            ${escaparHTML(producto.informacion || "Sin descripción")}
                        </span>

                    </div>

                </div>

            </td>


            <td>
                <span class="code">
                    ${escaparHTML(producto.codigo)}
                </span>
            </td>


            <td>
                Categoría #${producto.idCategoria ?? "N/A"}
            </td>


            <td>
                <strong>
                    ${formatearPrecio(producto.precio)}
                </strong>
            </td>


            <td>

                <div class="stock">

                    <div class="stock-info">

                        <strong>
                            ${producto.unidades}
                        </strong>

                        <span>
                            unidades
                        </span>

                    </div>

                    <div class="stock-bar">

                        <span
                            class="stock-fill ${stockClass}"
                            style="width: ${calcularStockPorcentaje(producto.unidades)}%"
                        ></span>

                    </div>

                </div>

            </td>


            <td>
                ${estado}
            </td>


            <td>

                <div class="product-actions">

                    <button
                        class="action-button edit"
                        data-id="${producto.id}"
                        title="Editar"
                    >
                        ✎
                    </button>

                    <button
                        class="action-button delete"
                        data-id="${producto.id}"
                        title="Eliminar"
                    >
                        ×
                    </button>

                </div>

            </td>

        `;


        tabla.appendChild(tr);

    });


    configurarAccionesProductos();
}


/*
|--------------------------------------------------------------------------
| EVENTOS DE PRODUCTOS
|--------------------------------------------------------------------------
*/

function configurarAccionesProductos() {

    document
        .querySelectorAll(".action-button.edit")
        .forEach(button => {

            button.addEventListener("click", () => {

                const id = Number(button.dataset.id);

                editarProducto(id);

            });

        });


    document
        .querySelectorAll(".action-button.delete")
        .forEach(button => {

            button.addEventListener("click", () => {

                const id = Number(button.dataset.id);

                eliminarProducto(id);

            });

        });
}


/*
|--------------------------------------------------------------------------
| EDITAR
|--------------------------------------------------------------------------
*/

function editarProducto(id) {

    const producto = productos.find(
        producto => producto.id === id
    );

    if (!producto) {
        return;
    }

    productoEditando = producto;

    abrirModal(producto);
}


/*
|--------------------------------------------------------------------------
| ELIMINAR
|--------------------------------------------------------------------------
*/

async function eliminarProducto(id) {

    const producto = productos.find(
        producto => producto.id === id
    );

    if (!producto) {
        return;
    }


    const confirmar = confirm(
        `¿Quieres eliminar "${producto.nombre}"?`
    );


    if (!confirmar) {
        return;
    }


    try {

        const response = await fetch(
            `${API_URL}/productos/${id}`,
            {
                method: "DELETE"
            }
        );


        const result = await response.json();


        if (!response.ok || !result.success) {

            throw new Error(
                result.message || "No se pudo eliminar"
            );
        }


        mostrarNotificacion(
            "Producto eliminado correctamente",
            "success"
        );


        await cargarProductos();


    } catch (error) {

        console.error(error);

        mostrarNotificacion(
            error.message,
            "error"
        );
    }
}


/*
|--------------------------------------------------------------------------
| CREAR / ACTUALIZAR
|--------------------------------------------------------------------------
*/

async function guardarProducto(event) {

    event.preventDefault();


    const form = event.target;


    const producto = {

        idCategoria:
            Number(form.idCategoria.value) || null,

        nombre:
            form.nombre.value.trim(),

        fechaVencimiento:
            form.fechaVencimiento.value,

        informacion:
            form.informacion.value.trim(),

        codigo:
            form.codigo.value.trim(),

        precio:
            Number(form.precio.value),

        unidades:
            Number(form.unidades.value),

        estado:
            form.estado.checked

    };


    try {

        let url = `${API_URL}/productos`;

        let method = "POST";


        if (productoEditando) {

            url =
                `${API_URL}/productos/${productoEditando.id}`;

            method = "PUT";

        }


        const response = await fetch(
            url,
            {
                method: method,

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify(producto)
            }
        );


        const result = await response.json();


        if (!response.ok || !result.success) {

            throw new Error(
                result.message || "No se pudo guardar el producto"
            );

        }


        cerrarModal();


        mostrarNotificacion(
            productoEditando
                ? "Producto actualizado correctamente"
                : "Producto creado correctamente",
            "success"
        );


        productoEditando = null;

        await cargarProductos();


    } catch (error) {

        console.error(error);

        mostrarNotificacion(
            error.message,
            "error"
        );
    }
}


/*
|--------------------------------------------------------------------------
| MODAL
|--------------------------------------------------------------------------
*/

function crearModal() {

    const modal = document.createElement("div");

    modal.id = "product-modal";

    modal.className = "modal-overlay";


    modal.innerHTML = `

        <div class="product-modal">

            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        INVENTARIO
                    </span>

                    <h2 id="modal-title">
                        Nuevo producto
                    </h2>

                </div>

                <button
                    type="button"
                    class="modal-close"
                    id="close-modal"
                >
                    ×
                </button>

            </div>


            <form id="product-form">

                <div class="form-grid">

                    <div class="form-group">

                        <label>
                            Nombre
                        </label>

                        <input
                            type="text"
                            name="nombre"
                            placeholder="Ej. Teclado mecánico"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Código
                        </label>

                        <input
                            type="text"
                            name="codigo"
                            placeholder="Ej. TEC001"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Categoría
                        </label>

                        <input
                            type="number"
                            name="idCategoria"
                            placeholder="ID categoría"
                            min="1"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Precio
                        </label>

                        <input
                            type="number"
                            name="precio"
                            placeholder="150000"
                            min="0"
                            step="0.01"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Unidades
                        </label>

                        <input
                            type="number"
                            name="unidades"
                            placeholder="10"
                            min="0"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Fecha de vencimiento
                        </label>

                        <input
                            type="date"
                            name="fechaVencimiento"
                            required
                        >

                    </div>


                    <div class="form-group full">

                        <label>
                            Información
                        </label>

                        <textarea
                            name="informacion"
                            rows="3"
                            placeholder="Descripción del producto..."
                        ></textarea>

                    </div>


                    <label class="switch-field">

                        <input
                            type="checkbox"
                            name="estado"
                            checked
                        >

                        <span class="switch"></span>

                        <span>
                            Producto activo
                        </span>

                    </label>

                </div>


                <div class="modal-actions">

                    <button
                        type="button"
                        class="cancel-button"
                        id="cancel-modal"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        Guardar producto
                    </button>

                </div>

            </form>

        </div>
    `;


    document.body.appendChild(modal);


    document
        .getElementById("product-form")
        .addEventListener(
            "submit",
            guardarProducto
        );


    document
        .getElementById("close-modal")
        .addEventListener(
            "click",
            cerrarModal
        );


    document
        .getElementById("cancel-modal")
        .addEventListener(
            "click",
            cerrarModal
        );


    modal.addEventListener(
        "click",
        event => {

            if (event.target === modal) {
                cerrarModal();
            }

        }
    );
}


/*
|--------------------------------------------------------------------------
| ABRIR MODAL
|--------------------------------------------------------------------------
*/

function abrirModal(producto = null) {

    const modal =
        document.getElementById("product-modal");

    const form =
        document.getElementById("product-form");

    const titulo =
        document.getElementById("modal-title");


    modal.classList.add("show");


    if (producto) {

        titulo.textContent =
            "Editar producto";


        form.nombre.value =
            producto.nombre;

        form.codigo.value =
            producto.codigo;

        form.idCategoria.value =
            producto.idCategoria ?? "";

        form.precio.value =
            producto.precio;

        form.unidades.value =
            producto.unidades;

        form.fechaVencimiento.value =
            producto.fechaVencimiento;

        form.informacion.value =
            producto.informacion || "";

        form.estado.checked =
            producto.estado;

    } else {

        titulo.textContent =
            "Nuevo producto";

        form.reset();

        form.estado.checked = true;

    }
}


/*
|--------------------------------------------------------------------------
| CERRAR MODAL
|--------------------------------------------------------------------------
*/

function cerrarModal() {

    const modal =
        document.getElementById("product-modal");

    modal.classList.remove("show");

    productoEditando = null;
}


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

function actualizarDashboard(lista) {

    const totalProductos =
        lista.length;


    const valorInventario =
        lista.reduce(
            (total, producto) =>
                total +
                (
                    Number(producto.precio) *
                    Number(producto.unidades)
                ),
            0
        );


    const stockBajo =
        lista.filter(
            producto =>
                producto.unidades <= 10
        ).length;


    const disponibles =
        lista.filter(
            producto =>
                producto.estado === true
        ).length;


    const porcentajeDisponibilidad =
        totalProductos === 0
            ? 0
            : (
                disponibles /
                totalProductos
            ) * 100;


    const numeros =
        document.querySelectorAll(
            ".stat-number"
        );


    if (numeros.length >= 4) {

        numeros[0].textContent =
            totalProductos;

        numeros[1].textContent =
            formatearPrecioCorto(
                valorInventario
            );

        numeros[2].textContent =
            stockBajo;

        numeros[3].textContent =
            `${porcentajeDisponibilidad.toFixed(1)}%`;

    }
}


/*
|--------------------------------------------------------------------------
| ALERTAS
|--------------------------------------------------------------------------
*/

function renderizarAlertas(lista) {

    const alertList =
        document.querySelector(".alert-list");

    if (!alertList) {
        return;
    }


    const productosBajoStock =
        lista
            .filter(
                producto =>
                    producto.unidades <= 10
            )
            .sort(
                (a, b) =>
                    a.unidades - b.unidades
            )
            .slice(0, 4);


    alertList.innerHTML = "";


    productosBajoStock.forEach(producto => {

        const div =
            document.createElement("div");


        div.className =
            "alert-item";


        div.innerHTML = `

            <div class="alert-product red">
                ${obtenerIniciales(producto.nombre)}
            </div>

            <div class="alert-info">

                <strong>
                    ${escaparHTML(producto.nombre)}
                </strong>

                <span>
                    Solo quedan ${producto.unidades} unidades
                </span>

            </div>

            <span class="alert-arrow">
                →
            </span>

        `;


        alertList.appendChild(div);

    });


    const contador =
        document.querySelector(".alert-count");


    if (contador) {

        contador.textContent =
            productosBajoStock.length;

    }
}


/*
|--------------------------------------------------------------------------
| BUSCADOR
|--------------------------------------------------------------------------
*/

if (buscador) {

    buscador.addEventListener(
        "input",
        event => {

            const texto =
                event.target.value
                    .toLowerCase()
                    .trim();


            const filtrados =
                productos.filter(producto =>

                    producto.nombre
                        .toLowerCase()
                        .includes(texto)

                    ||

                    producto.codigo
                        .toLowerCase()
                        .includes(texto)

                    ||

                    producto.informacion
                        .toLowerCase()
                        .includes(texto)

                );


            renderizarProductos(filtrados);

        }
    );
}


/*
|--------------------------------------------------------------------------
| BOTÓN AGREGAR
|--------------------------------------------------------------------------
*/

if (botonAgregar) {

    botonAgregar.addEventListener(
        "click",
        () => {

            abrirModal();

        }
    );

}


/*
|--------------------------------------------------------------------------
| UTILIDADES
|--------------------------------------------------------------------------
*/

function formatearPrecio(valor) {

    return new Intl.NumberFormat(
        "es-CO",
        {
            style: "currency",
            currency: "COP",
            maximumFractionDigits: 0
        }
    ).format(valor);

}


function formatearPrecioCorto(valor) {

    if (valor >= 1000000) {

        return `$${(
            valor / 1000000
        ).toFixed(1)}M`;

    }

    if (valor >= 1000) {

        return `$${(
            valor / 1000
        ).toFixed(0)}K`;

    }

    return formatearPrecio(valor);

}


function obtenerIniciales(nombre) {

    return nombre
        .split(" ")
        .slice(0, 2)
        .map(
            palabra =>
                palabra[0]
        )
        .join("")
        .toUpperCase();

}


function obtenerColorProducto(id) {

    const colores = [
        "purple-product",
        "blue-product",
        "pink-product",
        "orange-product"
    ];

    return colores[
        id % colores.length
    ];

}


function calcularStockPorcentaje(unidades) {

    const porcentaje =
        Number(unidades) * 4;

    return Math.min(
        Math.max(porcentaje, 5),
        100
    );

}


function escaparHTML(texto) {

    const div =
        document.createElement("div");

    div.textContent =
        texto ?? "";

    return div.innerHTML;

}


/*
|--------------------------------------------------------------------------
| NOTIFICACIONES
|--------------------------------------------------------------------------
*/

function mostrarNotificacion(
    mensaje,
    tipo = "success"
) {

    const notification =
        document.createElement("div");


    notification.className =
        `notification ${tipo}`;


    notification.innerHTML = `

        <span>
            ${tipo === "success" ? "✓" : "!"}
        </span>

        <strong>
            ${escaparHTML(mensaje)}
        </strong>

    `;


    document.body.appendChild(
        notification
    );


    setTimeout(
        () => {

            notification.classList.add(
                "hide"
            );

            setTimeout(
                () => notification.remove(),
                300
            );

        },
        3000
    );
}


/*
|--------------------------------------------------------------------------
| INICIALIZACIÓN
|--------------------------------------------------------------------------
*/

crearModal();

cargarProductos();