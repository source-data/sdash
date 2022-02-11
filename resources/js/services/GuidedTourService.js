const storageName = 'sdash-show-guided-tour';

export const getShowGuidedTour = () => {
  const guidedTourParameter = window.localStorage.getItem(storageName);
  if(guidedTourParameter === null || guidedTourParameter === 'true') return true;
  return false;
}

export const setShowGuidedTour = (tourValue) => {
  return window.localStorage.setItem(storageName, tourValue);
}