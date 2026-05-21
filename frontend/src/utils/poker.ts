const ranks = [
  'A',
  'K',
  'Q',
  'J',
  'T',
  '9',
  '8',
  '7',
  '6',
  '5',
  '4',
  '3',
  '2',
]

export const generateGrid = () => {

  const grid: string[][] = []

  for (let row = 0; row < ranks.length; row++) {

    const currentRow: string[] = []

    for (let col = 0; col < ranks.length; col++) {

      const first = ranks[row]
      const second = ranks[col]

      if (row === col) {
        currentRow.push(first + second)
      }

      else if (row < col) {
        currentRow.push(first + second + 's')
      }

      else {
        currentRow.push(second + first + 'o')
      }
    }

    grid.push(currentRow)
  }

  return grid
}