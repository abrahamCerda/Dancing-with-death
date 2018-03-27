<template>
    <div id="date-picker-info-container">
        <datepicker v-model="state.date"
                    calendar-class="custom-calendar"
                    wrapper-class="custom-wrapper"
                    v-bind:monday-first="true"
                    v-bind:full-month-name="true"
                    v-on:selected="dateClicked"
                    v-on:input="dateSelected"
                    v-bind:disabled="state.disabled"
                    v-bind:inline="true">
        </datepicker>

        <div id="instructions-container">
            <transition name="slide-fade"  v-on:afterLeave="displayHours">
                <p v-show="showInstructions" class="instructions">
                    - Select a date to schedule an appointment to dance with the Death.
                    <br>
                    <br>
                    <br>
                    - Remember that the Death only works on business days and only from
                    9:00 am to 6:00 pm.
                    <br>
                    <br>
                    <br>
                    - You have only one chance to dance with the Death, and it's of one hour of duration.
                    Seize it!
                </p>
            </transition>
            <hours
                    v-bind:freeHours="freeHours"
                    v-bind:showFreeHours="showFreeHours"
                    v-bind:selectedDate="selectedDate"
                    v-on:appointmentScheduled="appointmentScheduled">

            </hours>
        </div>
    </div>
</template>

<script>

    //we import the custom datepicker
    import Datepicker from 'vuejs-datepicker';
    //we import momentjs
    var moment = require('moment');

    //Here we define the component
    export default {
        //We this component as a sub component of this
        components: {
            Datepicker,

        },
        //data must be a function
        //that returns the real data
        data () {
            return {
                selectedDate: '',
                showFreeHours: false,
                showInstructions: true,
                freeHours: [],
                startTime : 9,
                endTime: 6,
                maxFreeHours: 9,
                minDate: '',
                state: {
                    disabled: {
                        to: new Date(moment().format('YYYY-MM-DD')),
                        days: [6, 0],
                    },
                }
            }
        },
        //Methods for this component
        methods: {
            //this method is called when the user has clicked a date from the calendar
            //but the input behind doesn't had the new date value
            dateClicked (){
                //console.log("Date clicked!!");
            },
            //this method is called when the value of the input has change
            //here we got the new date
            dateSelected(){
                //If we select a date, but the instructions aren't showing
                //this means that the free hours are shown
                //We hide the free hours in order to get the efect that the schedule of the date has changed
                if(this.showInstructions === false){
                    this.showFreeHours =false;
                }
                this.selectedDate = moment(this.state.date).format('YYYY-MM-DD');
                let vm = this;
                //We set the free hours, obtaining first the taken hours from the api
                //now we use the api
                axios.get('/appointments/' + this.selectedDate)
                    .then(function (response) {
                        //console.log(response.data);
                        //if we get the response from the api
                        //we set the free hours
                        vm.setFreeHours(response.data);
                        //when we get the free hours, if no instructions and hours are shown
                        //we show the free hours
                        if(vm.showFreeHours === false && vm.showInstructions === false){
                            vm.showFreeHours= true;
                        }
                        vm.showInstructions = false;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            //Method that calculate the free hours
            setFreeHours(appointments){
                //we clean the hours array
                this.freeHours= [];
                let i;
                let startTimeAux = this.startTime;
                let endTimeAux;
                //we start from 9 am, and use an aux variable to move from these time
                for (i=0; i < this.maxFreeHours; i++){
                    //if the start time is 13, we change to 1:00 pm
                    if(startTimeAux === 13){
                        startTimeAux = 1;
                    }
                    endTimeAux = startTimeAux +1;
                    if(endTimeAux === 13){
                        endTimeAux = 1;
                    }
                    let hour = {
                        id: i,
                        startHour : {
                            startTime: startTimeAux
                        },
                        endHour: {
                            endTime: endTimeAux
                        },
                        active: false
                    }
                    //We set the period of the start time and end time
                    //with less than 2, it is from 9 amm to 11 am hours
                    if(i < 2){
                        hour.startHour.period = 'am';
                        hour.endHour.period = 'am';
                    }
                    //with i == 2 ==> startime 11 am to 12 pm hour
                    else if( i === 2){
                        hour.startHour.period = 'am';
                        hour.endHour.period = 'pm';
                    }
                    //with i equal or bigger than 3, it is from 12 pm to 6pm hours
                    else if(i >= 3){
                        hour.startHour.period = 'pm';
                        hour.endHour.period = 'pm';
                    }
                    //If the created hour object doesn't have the same startTime
                    //with a fetched existing hour from the api
                    //we push it to the free hours array
                    let j;
                    let isFree = true;
                    for(j = 0; j < appointments.length; j++){
                        //console.log("appointment time: ");
                        //console.log(appointments[j].time);
                        //we pass the start time to 24 hrs format to compare
                        if(this.to24FormatString(hour) === appointments[j].time){
                            isFree = false;
                        }
                    }
                    if(isFree === true){
                        this.freeHours.push(hour);
                    }
                    //we add one hour to move to the next hour
                    startTimeAux=endTimeAux;
                }
                //console.log("Free hours: ");
                //console.log(this.freeHours);
            },
            displayHours: function (el) {
                this.showFreeHours= true;
            },
            //Here we don't need to include the 0 before the one digit hour
            to24FormatString(hour) {
                let hourAux;
                if(hour.startHour.period === 'pm'){
                    if(hour.startHour.startTime !== 12){
                        hourAux = hour.startHour.startTime + 12+":00:00";
                    }
                    else{
                        hourAux= 12 + ":00:00";
                    }
                }
                else{
                    if(hour.startHour.startTime < 10){
                        hourAux = "0"+hour.startHour.startTime +":00:00";
                    }
                    else {
                        hourAux = hour.startHour.startTime + ":00:00";
                    }
                }
                return hourAux;
            },
            //when an appointment is scheduled
            //we reload the free hours using the selectedDate method
            //the date still the same :)
            //we reload only if there's no error
            appointmentScheduled(){
                    this.dateSelected();
            }
        },
        //before the view is rendered
        created(){
            //we initialize the input with the today date
            let today = moment().format('YYYY-MM-DD');
            this.selectedDate = today;
            //and we set the min date from today
            this.minDate = today;
        },
        //when all the component is displayed
        mounted() {
            //console.log('Component mounted.');
        }
    }
</script>

<style type="text/css">

    *{
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
    }

    .custom-calendar{
        width: 100% !important;
        height: auto;
        padding:  0 5px 5px 5px !important;
        border-radius: 5px !important;
    }

    .custom-wrapper{
        padding:0 !important;
        margin:0 !important;
        width:50% !important;
        float:left !important;
        display:inline-block !important;
    }

    #instructions-container{
        width: 50%;
        padding: 0 3%;
        margin: 15px 0;
        display:inline-block;
        float: left;
    }

    .instructions{
        width:100%;
        margin:0 0 5% 0;
        font-size:1.125em;
        color:white;
        font-weight:700;
        text-align: left;
    }

    #date-picker-info-container{
        width: 100%;
        height:auto;
        overflow-x:hidden;
    }

    .slide-fade-enter-active {
        -webkit-transition: all .3s ease;
        -moz-transition: all .3s ease;
        transition: all .3s ease;
    }
    .slide-fade-leave-active {
        -webkit-transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
        -moz-transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active below version 2.1.8 */ {
        -webkit-transform: translateX(10px);
        -moz-transform: translateX(10px);
        transform: translateX(10px);
        opacity: 0;
    }

    /*Media query for responsive design*/
    @media screen and (max-width: 781px) {
        .custom-wrapper, #instructions-container{
            width:100%!important;
            float:none!important;
            display:block !important;
        }
    }

</style>