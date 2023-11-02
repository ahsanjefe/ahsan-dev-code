import { KeyedRule, SimpleRule } from 'vue3-form-validation'

const randomInt = (min: number, max: number) => {
  min = Math.ceil(min)
  max = Math.floor(max)
  return Math.floor(Math.random() * (max - min + 1)) + min
}

export type Lengthy = {
  length: number
}

export const rules = {
  required:
    (msg: string): SimpleRule =>
    x =>
      !x && msg,
  requiredIf:
    (msg: string, condition: boolean): SimpleRule =>
    x =>
      (!x && condition) && msg,
  min:
    (min: number) =>
    (msg: string): SimpleRule<Lengthy> =>
    x =>
      x.length >= min || msg,
  max:
    (max: number) =>
    (msg: string): SimpleRule<Lengthy> =>
    x =>
      x.length <= max || msg,
  minMax:
    (min: number, max: number) =>
    (msg: string): SimpleRule<Lengthy> =>
    x =>
      (min <= x.length && x.length <= max) || msg,
  email:
    (msg: string): SimpleRule<string> =>
    x =>
      /\S+@\S+\.\S+/.test(x) || msg,
  equal:
    (msg: string): KeyedRule =>
    (...xs) =>
      xs.every(x => x === xs[0]) || msg,
  allRequired:
    (msg: string): KeyedRule =>
    (...xs) => {
      for (const x of xs) {
        if (!x) {
          return msg
        }
      }
    },
  random:
    (min = 200, max = 2000): SimpleRule =>
    x =>
      new Promise<void | string>(resolve => {
        const ms = randomInt(min, max)
        setTimeout(() => {
          if (!x) {
            resolve(`Promise ${ms} ms`)
          } else {
            resolve()
          }
        }, ms)
      }),
  inTheFuture:
    (msg: string): SimpleRule<string> =>
    startDate => {
      const now = new Date().toLocaleDateString('en-CA')

      if (startDate.length === now.length && startDate < now) {
        return msg
      }
    }
}
