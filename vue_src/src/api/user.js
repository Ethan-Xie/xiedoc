import $http from '@/assets/js/http'

export function getCaptcha (mobile) {
  console.log('httping getCapcha')
  return $http.post('project/login/getCaptcha', {mobile: mobile})
}
