import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static values = {
        deal: Number
    }

    connect() {
        super.connect()
    }

    async like() {
        const response = await fetch(this.dealValue+"/like");
        if( response.statusCode === 204 ){
            console.log("OUI");
        }
        console.log(response.status);
    }
}
