import UserLevels from "./UserLevels";
import store from '@/stores/store';

const authGuards = (to, from, next) => {
  if(to.matched.some(routeItem => routeItem.meta.access === UserLevels.USER )) {
    if(store.getters.isLoggedIn) {
      next();
    } else {
      next({name:'login', query: {next: to.fullPath}});
    }
  } else {
    next();
  }

};

export default authGuards;