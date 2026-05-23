import api from './api'

export const getRangeGroups = async () => {

  const response = await api.get(
    '/range-groups'
  )

  return response.data
}

export const createRangeGroup = async (
  name: string
) => {

  const response = await api.post(
    '/range-groups',
    {
      name,
    }
  )

  return response.data
}