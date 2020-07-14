import Axios from 'axios'

export default function(message) {

  if(message.length === 0) throw "You attempted to submit an empty feedback message"

  return Axios.post("/feedback", {"message" : message})

}