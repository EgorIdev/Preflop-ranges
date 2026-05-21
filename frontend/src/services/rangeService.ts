import api from './api'

export const getRanges = async () => {
  const response = await api.get('/ranges')

  return response.data
}

export const createRange = async (
  name: string,
  spot: string
) => {
  const response = await api.post('/ranges', {
    name,
    spot,
  })

  return response.data
}

export const saveRangeItems = async (
  rangeId: number,
  items: {
    hand: string
    action: string
  }[]
) => {

  const response = await api.post(
    `/ranges/${rangeId}/items`,
    {
      items,
    }
  )

  return response.data
}