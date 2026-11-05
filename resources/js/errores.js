// saca el primer mensaje de error que mande Laravel (validación 422 o mensaje simple 409/403)
export function mensajeError(e) {
  const datos = e.response?.data
  if (datos?.errors) {
    return Object.values(datos.errors)[0][0]
  }
  return datos?.message || datos?.error || 'Ocurrió un error'
}
