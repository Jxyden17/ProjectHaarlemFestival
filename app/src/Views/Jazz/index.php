<div>welcome to the jazz homepage</div>
<div class="text-center"> 
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th> Photo </th>
                <th> time  </th>
                <th> location </th>
                 <th> name </th>
                <th> seats </th>
                <th> price </th>
                
            </tr>
        </thead>
        <tbody>
           <?php
                foreach ($jazzEvents as $jazzevent)
                {
                ?>
                <tr>
                    <td> <?=$jazzevent->getPhotoUrl()?> </td>
                    <td> <?=$jazzevent->getTime()?> </td>
                    <td><?=$jazzevent->getLocation()?> </td>
                    <td><?=$jazzevent->getArtistName()?> </td>
                    <td>
                        <div>
                            <?=$jazzevent->getFreeSeats()?>/
                            <?=$jazzevent->getTotalSeats()?> 
                        </div>
                    </td>
                    <td>€<?= number_format($jazzevent->getPrice(), 2) ?> </td>
                    <td>
                        <a href="/booking/jazz/<?=$jazzevent->getDay()?>/<?=$jazzevent->getArtistId()?>" class="btn btn-primary me-2">book now </a>       
                    </td>
                </tr>
               <?php }  ?>
        </tbody>
    </table>
</div>