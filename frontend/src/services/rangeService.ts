import api from './api'

export const getRanges = async () => {
  const response = await api.get('/ranges')

  return response.data
}

export const createRange = async (
  name: string,
  groupId: number | null
) => {
  const response = await api.post('/ranges', {
    name,
    group_id: groupId,
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

export const deleteRange = async (
  rangeId: number
) => {

  const response = await api.delete(
    `/ranges/${rangeId}`
  )

  return response.data
}