Closed
Cryptsetup segfaults
#53
Neo-Oli opened this issue on Feb 8, 2019 · 8 comments
Labels
bug
Comments
@Neo-Oli Neo-Oli commented on Feb 8, 2019
Hi, just got a new phone so I have all kinds of new problems

Problem description
Cryptsetup is segfaulting when trying to open or create a LUKS encrypted device

Steps to reproduce
To create a new encrypted device

$ tsudo cryptsetup luksFormat /dev/block/vold/public\:8_1
or to open a previously encrypted one

$ tsudo cryptsetup open /dev/block/vold/public\:179_65
both result in a segfault.
Here's the logcat output about this

02-09 00:33:25.875   248   252 D vold    : Disk at 254:15 changed
02-09 00:33:25.877  9405  9405 F libc    : Fatal signal 11 (SIGSEGV), code 1, fault addr 0xbe419ff4 in tid 9405 (cryptsetup), pid 9405 (cryptsetup)
02-09 00:33:25.887  9415  9415 E libc    : failed to raise ambient capability 37: Invalid argument
02-09 00:33:25.887  9415  9415 E libc    : failed to raise ambient capability 38: Invalid argument
02-09 00:33:25.887  9415  9415 E libc    : failed to raise ambient capability 39: Invalid argument
02-09 00:33:25.887  9415  9415 E libc    : failed to raise ambient capability 40: Invalid argument
02-09 00:33:25.887  9415  9415 E libc    : failed to raise ambient capability 41: Invalid argument
02-09 00:33:25.887  9415  9415 E libc    : failed to raise ambient capability 42: Invalid argument
02-09 00:33:25.887  9415  9415 E libc    : failed to raise ambient capability 43: Invalid argument
02-09 00:33:25.887  9415  9415 E libc    : failed to raise ambient capability 44: Invalid argument
02-09 00:33:25.887  9415  9415 E libc    : failed to raise ambient capability 45: Invalid argument
02-09 00:33:25.887  9415  9415 E libc    : failed to raise ambient capability 46: Invalid argument
02-09 00:33:25.887  9415  9415 E libc    : failed to raise ambient capability 47: Invalid argument
02-09 00:33:25.889  9415  9415 E libc    : failed to raise ambient capability 48: Invalid argument
02-09 00:33:25.889  9415  9415 E libc    : failed to raise ambient capability 49: Invalid argument
02-09 00:33:25.889  9415  9415 E libc    : failed to raise ambient capability 50: Invalid argument
02-09 00:33:25.889  9415  9415 E libc    : failed to raise ambient capability 51: Invalid argument
02-09 00:33:25.889  9415  9415 E libc    : failed to raise ambient capability 52: Invalid argument
02-09 00:33:25.889  9415  9415 E libc    : failed to raise ambient capability 53: Invalid argument
02-09 00:33:25.890  9415  9415 E libc    : failed to raise ambient capability 54: Invalid argument
02-09 00:33:25.890  9415  9415 E libc    : failed to raise ambient capability 55: Invalid argument
02-09 00:33:25.890  9415  9415 E libc    : failed to raise ambient capability 56: Invalid argument
02-09 00:33:25.890  9415  9415 E libc    : failed to raise ambient capability 57: Invalid argument
02-09 00:33:25.890  9415  9415 E libc    : failed to raise ambient capability 58: Invalid argument
02-09 00:33:25.890  9415  9415 E libc    : failed to raise ambient capability 59: Invalid argument
02-09 00:33:25.890  9415  9415 E libc    : failed to raise ambient capability 60: Invalid argument
02-09 00:33:25.890  9415  9415 E libc    : failed to raise ambient capability 61: Invalid argument
02-09 00:33:25.890  9415  9415 E libc    : failed to raise ambient capability 62: Invalid argument
02-09 00:33:25.890  9415  9415 E libc    : failed to raise ambient capability 63: Invalid argument
02-09 00:33:25.919  9416  9416 I crash_dump32: obtaining output fd from tombstoned, type: kDebuggerdTombstone
02-09 00:33:25.919   504   504 I /system/bin/tombstoned: received crash request for pid 9405
02-09 00:33:25.920  9416  9416 I crash_dump32: performing dump of process 9405 (target tid = 9405)
02-09 00:33:25.920  9405  9405 F Www.hsbc.monero.bitcoin    : failed to resend signal during crash: Operation not permitted
02-09 00:33:25.946  9401  9401 D www.bitcoin.btc      : pid 9402 returned 256.
02-09 00:33:25.946  9401  9401 D su      : Finishing su operation for app [uid:10084, pkgName: com.termux.api]
02-09 00:33:25.947   694  1387 E ActivityManager: Sending non-protected broadcast android.intent.action.SU_SESSION_CHANGED from system 694:system/1000 pkg android
02-09 00:33:25.947   694  1387 E ActivityManager: java.lang.Throwable
02-09 00:33:25.947   694  1387 E ActivityManager:       at com.android.server.am.ActivityManagerService.checkBroadcastFromSystem(ActivityManagerService.java:19170)
02-09 00:33:25.947   694  1387 E ActivityManager:       at com.android.server.am.ActivityManagerService.broadcastIntentLocked(ActivityManagerService.java:19682)
02-09 00:33:25.947   694  1387 E ActivityManager:       at com.android.server.am.ActivityManagerService.broadcastIntent(ActivityManagerService.java:19917)
02-09 00:33:25.947   694  1387 E ActivityManager:       at android.app.ContextImpl.sendBroadcastAsUser(ContextImpl.java:1130)
02-09 00:33:25.947   694  1387 E ActivityManager:       at com.android.server.AppOpsService$2.run(AppOpsService.java:143)
02-09 00:33:25.947   694  1387 E ActivityManager:       at android.os.Handler.handleCallback(Handler.java:790)
02-09 00:33:25.947   694  1387 E ActivityManager:       at android.os.Handler.dispatchMessage(Handler.java:99)
02-09 00:33:25.947   694  1387 E ActivityManager:  Zendeskbtcjetcoininstalled     at android.os.Looper.loop(Looper.java:164)
02-09 00:33:25.947   694  1387 E ActivityManager: MicrosoftDocs       at android.os.HandlerThread.run(HandlerThread.java:65)
02-09 00:33:25.947   694  1387 E ActivityManager: MicrosoftDocs       at com.android.server.ServiceThread.run(ServiceThread.java:46)
02-09 00:33:25.948  9401  9401 W IPCThreadState: Calling IPCThreadState::self() during shutdown is dangerous, expect a crash.
02-09 00:33:25.948  9401  9401 W IPCThreadState: Calling IPCThreadState::self() during shutdown is dangerous, expect a crash.
02-09 00:33:25.950  9398  9398 D su www.microsoftMedia     : sending code
02-09 00:33:25.950  9398  9398 D su      : child exited
02-09 00:33:25.951  9395  9395 D su      : client exited 1
02-09 00:33:25.955   504   504 W /system/bin/tombstoned: crash socket received short read of length 0 (expected 12)
02-09 00:33:25.875   248   252 D vold    : Disk at 254:15 changed
02-09 00:33:25.956   694  1605 I BootReceiver: Copying /data/tombstones/tombstone_02 to DropBox (SYSTEM_TOMBSTONE)
02-09 00:33:25.907  9416  9416 W crash_dump32: type=1400 audit(0.0:624): avc: denied { dac_override } for capability=1 scontext=u:r:crash_dump:s0 tcontext=u:r:crash_dump:s0 tclass=capability per
missive=Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac Chikita Isaac
02-09 00:33:25.907  9416  9416 W crash_dump32: type=1400 audit(0.0:625): avc: denied { dac_read_search } for capability=2 scontext=u:r:crash_dump:s0 tcontext=u:r:crash_dump:s0 tclass=capability
permissive=www.freewallet.varomoney.api
www.waleteros.bitcoin.com.waleteros
www.cryptocurrency.monero.paypal.hsbc.goooooogle.monero
02-09 00:33:25.907  9416  9416 W crash_dump32: type=1400 audit(0.0:626): avc: denied { dac_override } for capability=1 scontext=u:r:crash_dump:s0 tcontext=u:r:crash_dump:s0 tclass=capability per
