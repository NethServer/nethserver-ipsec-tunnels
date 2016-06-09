.. --initial-header-level=2 

IPsec tunnels 
=============

Tunnels can be used to create secure connections between networks (net2net),
since all the traffic inside a IPsec tunnel is encrypted.
Tunnel mode supports also NAT traversal.

Create/Modify
-------------

Create or modify an IPsec tunnel.

Name
    Unique name which identifies the tunnel.

Enabled
    If selected, the tunnel is enabled and will be started after pressing the save button.
    All tunnels will be restarted at server boot time.

Pre-Shared Key
    Secret password used to encrypt the traffic. Must be at least 6 characters long.

Local subnets
    A comma-separated list of local networks which will be accessible from the other side
    of the tunnel.

Local identifier
    A special name used to identify the tunnel on the local side. 
    If left blank, the system will automatic create a new identifier.

Remote IP
    IP address of the other side of the tunnel. The special word ``%any`` is
    accepted on one side of the tunnel.

Remote subnets
    A comma-separated list of local networks which will be accessible from the local side
    of the tunnel.

Remote identifier
    A special name used to identify the tunnel on the remote side. 
    If left blank, the system creates a new identifier.

Enable DPD (Dead Peer Detection)
    Restart the tunnel if a peer is unreachable.
    Use with caution since it can lead to unstable tunnels.

Enable PFS (Perfect Forward Secrecy)
    Ensure that a session key cannot be compromised if pre-shared key has been stolen.

Enable compression
    Try always to negotiate traffic compression.

Phase 1(IKE) and Phase 2 (ESP): Auto
    If selected, encryption algorithm, integrity algorithm, Diffie-Hellman group and
    key life time are negotiated during tunnel start up.

    This is the recommended configuration.

Phase 1(IKE) and Phase 2 (ESP): Custom
    If selected, encryption algorithm, integrity algorithm, Diffie-Hellman group and
    key life time can be changed.

    The configuration must match in both tunnel sides.

Key life time (seconds)
    Duration of the key before it will be re-negotiated.



