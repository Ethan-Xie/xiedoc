import $http from '@/assets/js/http'

export function getCaptcha (data) {
  console.log('test getCapcha')
  return $http.post('api/item/add', data)
}
