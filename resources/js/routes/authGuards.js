import UserLevels from "./UserLevels";
import store from '@/stores/store';

const authGuards = (to, from, next) => {
  console.log("Logged-in user?:" , store.getters.isLoggedIn);
  console.log("Route can be accessed by:" , to.matched[0].meta.access);
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