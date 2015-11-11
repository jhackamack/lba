# Notes

All references to "the laundry" shall refer to one or more pieces of clothing.  Items with specific types will be called out individually.
The system is location agnostic and can be moved freely.

# Requirements

## Laundry

**Question: When does the job to start laundry?**

* there is no pre-queue check for excess data stored temporarily in clothing items.
* Push / Pull mechanism for monitoring laundry completeness.
* Humidity index for restarting dryer.
* Safeway laundry detergent
* 1 dryer sheet.


## Dequeuing

* There is no check for excess data that might have slipped out of clothing items during dequeue operation.
* priority dequeuing based on visibility of items at the top most of the sort queue
* dequeuing is only complete when all items have been dequeued
* If a worker is interrupted during a dequeue operation the dequeue items are placed in a temp storage and the worker continues dequeue operation
* Socks are paired up before being completely dequeued.  During the dequeue operation if a sock doesn't have an immediate match it is stored in the temporary storage until a match is found.
* As items are dequeued they are evaluated using criteria defined below (see CQualityIndex below)

## Sorting

* Once all items have been dequeued they are reverse sorted so that the first item out of the dryer is the first item to be at the top of the queue.

## Persisting to Storage

There are many options for storage and the worker chooses the best location based on current factors including time, priority, and remaining energy.  The items can persist into the various areas described in the "Storage" section below.

Due to some location's storage limitations for things such as remaining height when stacking items like shirts or pants the worker may reorganize the items that are newer to persist in the primary active queue rather than relegating them to a secondary queue.

## Storage

### Dresser

* Dresser in three levels: pants, shirts, socks
* Shirts are organized into queues (active, inactive, cold weather (see conditions below))
* Each queue may contain multiple child queues if conditions for height are met: aka multiple active queues.

### Bed

The bed is used as a temporary space for storage, but not as a permeant storage facility.  Due to the auxiliary nature of the bed the storage space is used infrequently and shouldn't be relied upon for any short term or long term storage needs.  The bed is cleaned up in a more periodic fashion than the floor and should only be relied upon as a last resort (See "Jobs::Cleanup").

### Floor

The floor is used as a temporary space for storage, but not as a permeant storage facility.  Due to the limited persistent nature of the storage space the floor is periodically cleaned up and can't be depended on for long term items (See "Jobs::Cleanup").

## Workers

*** Question: Is the read / write lock universal or per data section?***

There can be one or more workers (but no less than one) worker operating.  Due to the necessity of space and differing opinions on priority of items (see "Sorting" for priority descriptions) the workers need to be able to block other workers from processing items in parallel.  The workers retain a read/write lock file to prevent parallel actions from conflicting with the overall process.

## Jobs

### Dequeue

During dequeuing items from the dryer the job must complete de-queuing all items before it moves on to the sorting operation.  If a worker gets distracted or killed in place the de-queued items are placed in the temporary space until the worker is restarted.  After restart the remaining items are de-queued and the items move into the sorting algorithm. 

### Cleanup

*** Question: what triggers the cleanup jobs?***

The cleanup jobs run on an arbitrary schedule that move items from the temporary storage of the floor and / or the bed to more permeant storage queues in the dresser.

### Weather Monitor

*** Question: is it a push/pull weather monitor? How is the system notified of temperature changes.***

*** Question: When activating is it particular items or is it primary queue based activation that takes so long***

The weather monitor continuously polls the public weather feed for information.  It stores only 7 days worth of previous data and calculates a 7 day moving average based on the information.  If the 7 day moving average falls below or, is equal to, 79 degrees Fahrenheit then it begins a multiples activation of the cold weather queue(s).  The multiples activation can take a few cycles to complete so the activation isn't instant.   

## Indexes

### HumidityIndex

***Question: If one item is not dry but the rest are is the whole batch re-enqued? ***

The HumidityIndex ensures that items have achieved the right dryness factor.  If an item exceeds the HumidityIndex of 0.3% then the item is re-enqueed in the dryer for further dehumidifying.

### CQualityIndex

The CQualityIndex defines the quality of a particular clothing item.  For shirts and pants the CQualityIndex is defined as the condition of the clothing from every day wear and tear to unexpected marks and or perforations.  For socks the conditions are defined as an Elasticity Quality which is normally decreased after the term of one (1) year.  If a sock pair has one sock with a low CQualityIndex the pair is considered bad.  Upon a CQualityIndex of 0.8 or lower the items are removed from circulation.
