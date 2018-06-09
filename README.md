# Using the Agate plugin for ZenCart

## Prerequisites

* Last Cart Version Tested: 1.5.1

You must have a Agate API KEY to use this plugin.  It's free visit [here](http://www.agate.services/registration-form/) .

## Installation

Download the zip file for this plugin, unzip the archive and copy the files into the ZenCart directory on your webserver.

## Configuration

* In Admin panel under "Modules > Payment > Agate" click Install.
* Fill out all configuration information:
  * Verify that the module is enabled.
  * Enter the API KEY.
  * Choose a sort order for displaying this payment option to visitors.  Lowest is displayed first.<br />

## Usage

When a shopper chooses the Agate payment method, they will be presented with an order summary as the next step (prices are shown in whatever currency they've selected for shopping). Upon receiving their order, the system takes the shopper to a agate.services invoice where the user is presented with payment instructions.  Once payment is received, a link is presented to the shopper that will take them back to thier website.

In your Admin control panel, you can see the orders made with Agate just as you would any other order.  The status you selected in the configuration steps above will indicate whether the order has been paid for. 
